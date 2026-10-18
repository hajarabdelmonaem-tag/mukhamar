<?php

use Anthropic\Core\Exceptions\AnthropicException;
use App\Models\Address;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\ChatbotClient;
use App\Services\ChatbotToolsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'services.chatbot.enabled' => true,
        'services.chatbot.driver' => 'fake',
    ]);
});

/**
 * Bind a scripted model client that returns the given turns in order and
 * records every request it receives.
 *
 * @param  list<array<string, mixed>>  $turns
 */
function scriptChatbot(array $turns): object
{
    $client = new class($turns) implements ChatbotClient
    {
        /** @var list<array<string, mixed>> */
        public array $requests = [];

        public function __construct(private array $turns) {}

        public function send(array $system, array $tools, array $messages, bool $allowTools = true): array
        {
            $this->requests[] = compact('system', 'messages', 'allowTools');
            $turn = array_shift($this->turns) ?? end($this->requests);

            if ($turn instanceof Throwable) {
                throw $turn;
            }

            return $turn + ['stop_reason' => 'end_turn', 'input_tokens' => 10, 'output_tokens' => 5];
        }
    };

    app()->instance(ChatbotClient::class, $client);

    return $client;
}

/**
 * @return array<string, mixed>
 */
function textTurn(string $text): array
{
    return ['content' => [['type' => 'text', 'text' => $text]], 'stop_reason' => 'end_turn'];
}

/**
 * @param  array<string, mixed>  $input
 * @return array<string, mixed>
 */
function toolTurn(string $name, array $input = []): array
{
    return [
        'content' => [['type' => 'tool_use', 'id' => 'toolu_'.uniqid(), 'name' => $name, 'input' => $input]],
        'stop_reason' => 'tool_use',
    ];
}

it('answers a guest in fake mode using real product data', function (): void {
    $product = Product::factory()->create(['name' => ['en' => 'Royal Oud', 'ar' => 'عود ملكي'], 'is_featured' => true]);

    $response = $this->postJson('/api/v1/chat', ['message' => 'عندكم عطور حلوة؟']);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['conversation_id', 'reply' => ['id', 'role', 'text', 'created_at']]])
        ->assertJsonPath('data.reply.role', 'assistant');

    expect($response->json('data.reply.text'))
        ->toContain('Royal Oud')
        ->not->toContain('{"');

    $conversation = ChatConversation::query()->where('uuid', $response->json('data.conversation_id'))->firstOrFail();
    expect($conversation->user_id)->toBeNull()
        ->and($conversation->messages()->count())->toBe(4)
        ->and($conversation->visibleMessages()->count())->toBe(2);
});

it('sends the stored history back to the model when a conversation continues', function (): void {
    $client = scriptChatbot([textTurn('Hello!'), textTurn('Sure.')]);

    $first = $this->postJson('/api/v1/chat', ['message' => 'Hi'])->assertOk();

    $this->postJson('/api/v1/chat', [
        'message' => 'Recommend something',
        'conversation_id' => $first->json('data.conversation_id'),
    ])->assertOk()->assertJsonPath('data.reply.text', 'Sure.');

    expect($client->requests[1]['messages'])->toBe([
        ['role' => 'user', 'content' => [['type' => 'text', 'text' => 'Hi']]],
        ['role' => 'assistant', 'content' => [['type' => 'text', 'text' => 'Hello!']]],
        ['role' => 'user', 'content' => [['type' => 'text', 'text' => 'Recommend something']]],
    ]);
});

it('looks up only the logged-in customer orders', function (): void {
    $customer = User::factory()->create();
    $mine = Order::factory()->create(['user_id' => $customer->id, 'order_no' => 'MKR-1111']);
    Order::factory()->create(['order_no' => 'MKR-2222']);

    $this->actingAs($customer, 'sanctum');

    $response = $this->postJson('/api/v1/chat', ['message' => 'فين طلبي؟'])->assertOk();

    expect($response->json('data.reply.text'))
        ->toContain($mine->order_no)
        ->not->toContain('MKR-2222');

    expect(ChatConversation::query()->sole()->user_id)->toBe($customer->id);
});

it('asks a guest to log in when they ask about orders in fake mode', function (): void {
    $this->postJson('/api/v1/chat', ['message' => 'Where is my order?'])
        ->assertOk()
        ->assertJsonPath('data.reply.text', __('api.chatbot.fake.header')."\n\n".__('api.chatbot.fake.login_required'));
});

it('refuses order lookups for guests', function (): void {
    $order = Order::factory()->create();

    $result = app(ChatbotToolsService::class)->execute('get_order_details', ['order_no' => $order->order_no], null);

    expect($result)->toHaveKey('error');
});

it('does not reveal another customer order by its number', function (): void {
    $order = Order::factory()->create();

    $result = app(ChatbotToolsService::class)
        ->execute('get_order_details', ['order_no' => $order->order_no], User::factory()->create());

    expect($result)->toHaveKey('error');
});

it('searches products by fragrance notes in arabic', function (): void {
    $oud = Product::factory()->create(['base_notes' => ['en' => 'Cambodian Oud', 'ar' => 'عود كمبودي']]);
    Product::factory()->create([
        'name' => ['en' => 'Fresh', 'ar' => 'منعش'],
        'description' => ['en' => 'Citrus', 'ar' => 'حمضيات'],
        'top_notes' => ['en' => 'Lemon', 'ar' => 'ليمون'],
        'heart_notes' => ['en' => 'Jasmine', 'ar' => 'ياسمين'],
        'base_notes' => ['en' => 'Musk', 'ar' => 'مسك'],
    ]);

    $result = app(ChatbotToolsService::class)->execute('search_products', ['query' => 'كمبودي'], null);

    expect($result['count'])->toBe(1)
        ->and($result['products'][0]['id'])->toBe($oud->id);
});

it('returns the most recently added product first when sorting by newest', function (): void {
    $createdAt = now();
    Product::factory()->create(['created_at' => $createdAt, 'is_featured' => true]);
    $newest = Product::factory()->create(['created_at' => $createdAt, 'is_featured' => false]);

    $result = app(ChatbotToolsService::class)->execute('search_products', ['sort' => 'newest', 'limit' => 1], null);

    expect($result['products'][0]['id'])->toBe($newest->id);
});

it('answers which product was added last in fake mode', function (): void {
    Product::factory()->create(['name' => ['en' => 'Old Featured', 'ar' => 'قديم'], 'is_featured' => true]);
    Product::factory()->create(['name' => ['en' => 'Brand New Oud', 'ar' => 'عود جديد'], 'is_featured' => false]);

    $reply = $this->postJson('/api/v1/chat', ['message' => 'ايه اخر منتج اتضاف؟'])
        ->assertOk()
        ->json('data.reply.text');

    expect($reply)->toContain('Brand New Oud')->not->toContain('Old Featured');
});

it('reads price limits and notes from a product question in fake mode', function (): void {
    Product::factory()->create(['name' => ['en' => 'Cheap Oud', 'ar' => 'عود'], 'price' => 150, 'base_notes' => ['en' => 'Oud', 'ar' => 'عود']]);
    Product::factory()->create(['name' => ['en' => 'Pricey Oud', 'ar' => 'عود غالي'], 'price' => 900, 'base_notes' => ['en' => 'Oud', 'ar' => 'عود']]);
    Product::factory()->create(['name' => ['en' => 'Cheap Rose', 'ar' => 'ورد'], 'price' => 120, 'top_notes' => ['en' => 'Rose', 'ar' => 'ورد'], 'heart_notes' => ['en' => 'Rose', 'ar' => 'ورد'], 'base_notes' => ['en' => 'Musk', 'ar' => 'مسك']]);

    $reply = $this->postJson('/api/v1/chat', ['message' => 'عايزة عطر عود تحت ٣٠٠'])
        ->assertOk()
        ->json('data.reply.text');

    expect($reply)->toContain('Cheap Oud')
        ->not->toContain('Pricey Oud')
        ->not->toContain('Cheap Rose');
});

it('lists only products with the bestseller badge as best sellers', function (): void {
    $bestSeller = Product::factory()->create(['badges' => ['bestseller']]);
    Product::factory()->create(['badges' => ['new']]);

    $result = app(ChatbotToolsService::class)->execute('get_best_sellers', [], null);

    expect($result['products'])->toHaveCount(1)
        ->and($result['products'][0]['id'])->toBe($bestSeller->id);
});

it('lists active banners and discounted products as offers without coupon codes', function (): void {
    Banner::factory()->create(['title' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف']]);
    Banner::factory()->inactive()->create(['title' => ['en' => 'Old Banner', 'ar' => 'قديم']]);
    $onSale = Product::factory()->create(['price' => 80, 'old_price' => 100]);
    Product::factory()->create(['old_price' => null]);
    Coupon::factory()->create(['code' => 'SECRET50']);

    $result = app(ChatbotToolsService::class)->execute('get_current_offers', [], null);

    expect(collect($result['banners'])->pluck('title')->all())->toBe(['Summer Sale'])
        ->and($result['products_on_sale'])->toHaveCount(1)
        ->and($result['products_on_sale'][0]['id'])->toBe($onSale->id)
        ->and($result['products_on_sale'][0]['discount_percent'])->toBe(20)
        ->and(json_encode($result))->not->toContain('SECRET50');
});

it('checks coupon codes and explains why one does not work', function (): void {
    $tools = app(ChatbotToolsService::class);
    Coupon::factory()->create(['code' => 'SAVE10', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_subtotal' => null, 'max_uses' => null]);
    Coupon::factory()->expired()->create(['code' => 'OLD20']);

    expect($tools->execute('check_coupon', ['code' => 'SAVE10'], null))
        ->valid->toBeTrue()
        ->discount_value->toBe(10.0)
        ->and($tools->execute('check_coupon', ['code' => 'OLD20'], null))
        ->valid->toBeFalse()
        ->reason->toBe('expired')
        ->and($tools->execute('check_coupon', ['code' => 'NOPE'], null))
        ->reason->toBe('not_found');
});

it('checks a coupon minimum against the customer cart', function (): void {
    $customer = User::factory()->create();
    Coupon::factory()->create(['code' => 'BIG500', 'min_subtotal' => 500, 'max_uses' => null]);
    CartItem::factory()->create([
        'cart_id' => Cart::factory()->create(['user_id' => $customer->id])->id,
        'product_id' => Product::factory()->create(['price' => 100])->id,
        'product_variant_id' => null,
        'quantity' => 1,
    ]);

    expect(app(ChatbotToolsService::class)->execute('check_coupon', ['code' => 'BIG500'], $customer))
        ->valid->toBeFalse()
        ->reason->toBe('cart_below_minimum');
});

it('returns only approved product reviews', function (): void {
    $product = Product::factory()->create();
    ProductReview::factory()->approved()->create(['product_id' => $product->id, 'body' => 'Lovely scent']);
    ProductReview::factory()->create(['product_id' => $product->id, 'is_approved' => false, 'body' => 'Hidden review']);

    $result = app(ChatbotToolsService::class)->execute('get_product_reviews', ['product_id' => $product->id], null);

    expect(collect($result['reviews'])->pluck('body')->all())->toBe(['Lovely scent']);
});

it('returns the store contact details from settings', function (): void {
    Setting::query()->create(['key' => 'site_email', 'value' => 'info@mukhamar.com']);
    Setting::query()->create(['key' => 'socials', 'value' => [['name' => ['en' => 'Instagram', 'ar' => 'انستغرام'], 'link' => 'https://instagram.com/mukhamar']]]);

    expect(app(ChatbotToolsService::class)->execute('get_contact_info', [], null))
        ->email->toBe('info@mukhamar.com')
        ->socials->toBe([['name' => 'Instagram', 'link' => 'https://instagram.com/mukhamar']]);
});

it('returns only the logged-in customer wishlist, addresses and notifications', function (): void {
    $tools = app(ChatbotToolsService::class);
    $customer = User::factory()->create();
    $other = User::factory()->create();
    $mine = Wishlist::factory()->create(['user_id' => $customer->id]);
    Wishlist::factory()->create(['user_id' => $other->id]);
    Address::factory()->default()->create(['user_id' => $customer->id, 'street' => 'My Street']);
    Address::factory()->create(['user_id' => $other->id, 'street' => 'Other Street']);
    Notification::factory()->create(['user_id' => $customer->id, 'title' => 'Mine', 'read_at' => null]);
    Notification::factory()->create(['user_id' => $other->id, 'title' => 'Theirs']);

    expect(collect($tools->execute('get_my_wishlist', [], $customer)['products'])->pluck('id')->all())->toBe([$mine->product_id])
        ->and($tools->execute('get_my_addresses', [], $customer)['addresses'])->toHaveCount(1)
        ->and($tools->execute('get_my_addresses', [], $customer)['addresses'][0]['street'])->toBe('My Street')
        ->and($tools->execute('get_my_notifications', [], $customer))
        ->unread_count->toBe(1)
        ->notifications->toHaveCount(1);
});

it('returns the logged-in customer account details', function (): void {
    $customer = User::factory()->create(['name' => 'Sara']);
    Order::factory()->count(2)->create(['user_id' => $customer->id]);

    expect(app(ChatbotToolsService::class)->execute('get_my_account', [], $customer))
        ->name->toBe('Sara')
        ->orders_count->toBe(2);
});

it('asks guests to log in for personal tools', function (string $tool): void {
    expect(app(ChatbotToolsService::class)->execute($tool, [], null))->toHaveKey('error');
})->with(['get_my_wishlist', 'get_my_addresses', 'get_my_notifications', 'get_my_account']);

it('routes coupon, offer and contact questions in fake mode', function (): void {
    Coupon::factory()->create(['code' => 'SAVE10', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_subtotal' => null, 'max_uses' => null]);
    Setting::query()->create(['key' => 'site_phone', 'value' => '+966 50 000 0000']);
    Product::factory()->create(['name' => ['en' => 'Sale Oud', 'ar' => 'عود مخفض'], 'price' => 80, 'old_price' => 100]);

    expect($this->postJson('/api/v1/chat', ['message' => 'الكود SAVE10 شغال؟'])->json('data.reply.text'))
        ->toContain(__('api.chatbot.fake.coupon_valid', ['code' => 'SAVE10', 'discount' => '10%']))
        ->and($this->postJson('/api/v1/chat', ['message' => 'فيه عروض؟'])->json('data.reply.text'))
        ->toContain('Sale Oud')
        ->and($this->postJson('/api/v1/chat', ['message' => 'عايزة اتواصل معاكم'])->json('data.reply.text'))
        ->toContain('+966 50 000 0000');
});

it('sends a support message for a logged-in customer', function (): void {
    $customer = User::factory()->create();

    app(ChatbotToolsService::class)->execute('contact_support', ['message' => 'I want to change my address'], $customer);

    expect(ContactMessage::query()->sole())
        ->email->toBe($customer->email)
        ->message->toBe('I want to change my address');
});

it('blocks access to another customer conversation', function (): void {
    $conversation = ChatConversation::factory()->create(['user_id' => User::factory()->create()->id]);

    $this->actingAs(User::factory()->create(), 'sanctum');

    $this->getJson("/api/v1/chat/{$conversation->uuid}")->assertForbidden();
    $this->postJson('/api/v1/chat', ['message' => 'Hi', 'conversation_id' => $conversation->uuid])->assertForbidden();
});

it('returns the visible messages of a conversation', function (): void {
    $conversation = ChatConversation::factory()->create();
    ChatMessage::factory()->for($conversation, 'conversation')->create(['text' => 'Hi']);
    ChatMessage::factory()->for($conversation, 'conversation')->assistant()->create([
        'content' => [['type' => 'tool_use', 'id' => 'toolu_1', 'name' => 'list_categories', 'input' => []]],
        'text' => null,
    ]);

    $this->getJson("/api/v1/chat/{$conversation->uuid}")
        ->assertOk()
        ->assertJsonCount(1, 'data.messages')
        ->assertJsonPath('data.messages.0.text', 'Hi');
});

it('stops a conversation at the turn limit', function (): void {
    config(['services.chatbot.max_turns' => 1]);
    $conversation = ChatConversation::factory()->create();
    ChatMessage::factory()->for($conversation, 'conversation')->create();

    $this->postJson('/api/v1/chat', ['message' => 'Hi', 'conversation_id' => $conversation->uuid])
        ->assertUnprocessable();
});

it('does not store an exchange the model refused', function (): void {
    scriptChatbot([['content' => [], 'stop_reason' => 'refusal']]);

    $this->postJson('/api/v1/chat', ['message' => 'Hi'])
        ->assertOk()
        ->assertJsonPath('data.reply.text', __('api.chatbot.cannot_help'));

    expect(ChatMessage::query()->count())->toBe(0);
});

it('stops calling tools after the round limit', function (): void {
    config(['services.chatbot.max_tool_rounds' => 2]);
    $client = scriptChatbot([
        toolTurn('list_categories'),
        toolTurn('list_categories'),
        toolTurn('list_categories'),
    ]);

    $this->postJson('/api/v1/chat', ['message' => 'Hi'])
        ->assertOk()
        ->assertJsonPath('data.reply.text', __('api.chatbot.cannot_help'));

    expect($client->requests)->toHaveCount(3)
        ->and($client->requests[2]['allowTools'])->toBeFalse()
        ->and(ChatMessage::query()->count())->toBe(0);
});

it('returns 503 when the model api fails', function (): void {
    scriptChatbot([new AnthropicException('Connection failed')]);

    $this->postJson('/api/v1/chat', ['message' => 'Hi'])->assertServiceUnavailable();

    expect(ChatMessage::query()->count())->toBe(0);
});

it('returns 503 when the chatbot is disabled', function (): void {
    config(['services.chatbot.enabled' => false]);

    $this->postJson('/api/v1/chat', ['message' => 'Hi'])->assertServiceUnavailable();
});

it('validates the message length', function (): void {
    config(['services.chatbot.max_message_length' => 5]);

    $this->postJson('/api/v1/chat', ['message' => 'Too long message'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('message');
});

it('shows conversations in the admin panel', function (): void {
    $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    $conversation = ChatConversation::factory()->create();
    ChatMessage::factory()->for($conversation, 'conversation')->create(['text' => 'عايزة عطر عود']);

    $this->actingAs($admin)->get(route('admin.chat-conversations.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.chat-conversations.show', $conversation))
        ->assertOk()
        ->assertSee('عايزة عطر عود');
});
