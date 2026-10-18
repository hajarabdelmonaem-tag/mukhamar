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
use App\Models\Intro;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\ChatbotClient;
use App\Services\ChatbotService;
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
        ->toContain('عود ملكي')
        ->not->toContain('{"');

    $conversation = ChatConversation::query()->where('uuid', $response->json('data.conversation_id'))->firstOrFail();
    expect($conversation->user_id)->toBeNull()
        ->and($conversation->messages()->count())->toBe(4)
        ->and($conversation->visibleMessages()->count())->toBe(2);
});

it('replies with what it can help with when fake mode does not recognise a message', function (string $message): void {
    Product::factory()->create();

    $this->postJson('/api/v1/chat', ['message' => $message])
        ->assertOk()
        ->assertJsonPath('data.reply.text', __('api.chatbot.fake.header')."\n\n".__('api.chatbot.fake.help'));

    expect(ChatMessage::query()->count())->toBe(2);
})->with(['i need a help', 'مرحبا', 'hello']);

it('replies in the language of the message, not the app locale', function (string $locale, string $message, string $expectedLanguage, string $expectedName): void {
    Product::factory()->create(['name' => ['en' => 'Royal Oud', 'ar' => 'عود ملكي']]);

    $reply = $this->withHeader('X-Locale', $locale)
        ->postJson('/api/v1/chat', ['message' => $message])
        ->assertOk()
        ->json('data.reply.text');

    expect($reply)
        ->toStartWith(trans('api.chatbot.fake.header', locale: $expectedLanguage))
        ->toContain($expectedName);
})->with([
    'arabic message, english app' => ['en', 'عايزة عطر', 'ar', 'عود ملكي'],
    'english message, arabic app' => ['ar', 'I want a perfume', 'en', 'Royal Oud'],
]);

it('keeps the app locale when a message has no letters', function (): void {
    $this->withHeader('X-Locale', 'ar')
        ->postJson('/api/v1/chat', ['message' => '123'])
        ->assertOk()
        ->assertJsonPath('data.reply.text', trans('api.chatbot.fake.header', locale: 'ar')."\n\n".trans('api.chatbot.fake.help', locale: 'ar'));
});

it('detects the language from the dominant alphabet', function (string $text, ?string $language): void {
    expect(ChatbotService::languageOf($text))->toBe($language);
})->with([
    ['الكود SAVE10 شغال؟', 'ar'],
    ['is the oud perfume in stock', 'en'],
    ['؟؟ 123', 'ar'],
    ['123 !!', null],
]);

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
    Product::factory()->create(['name' => ['en' => 'Old Featured', 'ar' => 'المميز القديم'], 'is_featured' => true]);
    Product::factory()->create(['name' => ['en' => 'Brand New Oud', 'ar' => 'الإصدار الجديد'], 'is_featured' => false]);

    $reply = $this->postJson('/api/v1/chat', ['message' => 'ايه اخر منتج اتضاف؟'])
        ->assertOk()
        ->json('data.reply.text');

    expect($reply)->toContain('الإصدار الجديد')->not->toContain('المميز القديم');
});

it('reads price limits and notes from a product question in fake mode', function (): void {
    Product::factory()->create(['name' => ['en' => 'Cheap Oud', 'ar' => 'سحر الشرق'], 'price' => 150, 'base_notes' => ['en' => 'Oud', 'ar' => 'عود']]);
    Product::factory()->create(['name' => ['en' => 'Pricey Oud', 'ar' => 'فخامة الملوك'], 'price' => 900, 'base_notes' => ['en' => 'Oud', 'ar' => 'عود']]);
    Product::factory()->create(['name' => ['en' => 'Cheap Rose', 'ar' => 'نسمة الربيع'], 'price' => 120, 'top_notes' => ['en' => 'Rose', 'ar' => 'ورد'], 'heart_notes' => ['en' => 'Rose', 'ar' => 'ورد'], 'base_notes' => ['en' => 'Musk', 'ar' => 'مسك']]);

    $reply = $this->postJson('/api/v1/chat', ['message' => 'عايزة عطر عود تحت ٣٠٠'])
        ->assertOk()
        ->json('data.reply.text');

    expect($reply)->toContain('سحر الشرق')
        ->not->toContain('فخامة الملوك')
        ->not->toContain('نسمة الربيع');
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
        ->toContain('عود مخفض')
        ->and($this->postJson('/api/v1/chat', ['message' => 'عايزة اتواصل معاكم'])->json('data.reply.text'))
        ->toContain('+966 50 000 0000');
});

it('returns the app information from settings and intro screens', function (): void {
    Setting::query()->create(['key' => 'site_name', 'value' => 'Mukhamar']);
    Setting::query()->create(['key' => 'maintenance_mode', 'value' => 'false']);
    Setting::query()->create(['key' => 'terms_conditions', 'value' => ['title' => ['en' => 'Terms'], 'sections' => []]]);
    Intro::query()->create(['title' => ['en' => 'Discover oud', 'ar' => 'اكتشف العود'], 'is_active' => true]);
    Intro::query()->create(['title' => ['en' => 'Hidden slide', 'ar' => 'مخفي'], 'is_active' => false]);

    expect(app(ChatbotToolsService::class)->execute('get_app_info', [], null))
        ->app_name->toBe('Mukhamar')
        ->under_maintenance->toBeFalse()
        ->legal_pages->toBe(['terms' => true, 'privacy' => false])
        ->and(collect(app(ChatbotToolsService::class)->execute('get_app_info', [], null)['features'])->pluck('title')->all())
        ->toBe(['Discover oud']);
});

it('returns every section of a policy page in the current language', function (): void {
    app()->setLocale('ar');
    Setting::query()->create(['key' => 'terms_conditions', 'value' => [
        'title' => ['en' => 'Terms of Use', 'ar' => 'شروط الاستخدام'],
        'sections' => collect(range(1, 4))->map(fn (int $i): array => [
            'heading' => ['en' => "Section {$i}", 'ar' => "القسم {$i}"],
            'body' => ['en' => "Body {$i}", 'ar' => "النص {$i}"],
        ])->all(),
    ]]);

    $result = app(ChatbotToolsService::class)->execute('get_policy', ['page' => 'terms'], null);

    expect($result['title'])->toBe('شروط الاستخدام')
        ->and($result['sections'])->toHaveCount(4)
        ->and($result['sections'][3])->toBe(['heading' => 'القسم 4', 'body' => 'النص 4']);
});

it('reads a policy page saved in the older single-body format', function (): void {
    Setting::query()->create(['key' => 'policy', 'value' => ['en' => 'We keep your data safe.', 'ar' => 'نحافظ على بياناتك.']]);

    expect(app(ChatbotToolsService::class)->execute('get_policy', ['page' => 'privacy'], null)['sections'])
        ->toBe([['heading' => null, 'body' => 'We keep your data safe.']]);
});

it('says when a policy page is not published', function (): void {
    expect(app(ChatbotToolsService::class)->execute('get_policy', ['page' => 'privacy'], null))->toHaveKey('error');
});

it('answers app name and terms of use questions in fake mode', function (): void {
    Setting::query()->create(['key' => 'site_name', 'value' => 'Mukhamar']);
    Setting::query()->create(['key' => 'terms_conditions', 'value' => [
        'title' => ['en' => 'Terms of Use', 'ar' => 'شروط الاستخدام'],
        'sections' => [['heading' => ['en' => 'Orders', 'ar' => 'الطلبات'], 'body' => ['en' => 'Orders can be cancelled before shipping.', 'ar' => 'يمكن إلغاء الطلب قبل الشحن.']]],
    ]]);

    expect($this->postJson('/api/v1/chat', ['message' => 'ايه اسم التطبيق؟'])->json('data.reply.text'))
        ->toContain('Mukhamar')
        ->and($this->postJson('/api/v1/chat', ['message' => 'ايه سياسة الاستخدام؟'])->json('data.reply.text'))
        ->toContain('يمكن إلغاء الطلب قبل الشحن.')
        ->and($this->postJson('/api/v1/chat', ['message' => 'what are the terms of use?'])->json('data.reply.text'))
        ->toContain('Orders can be cancelled before shipping.');
});

it('returns the tool key and the products in the products api format', function (): void {
    $older = Product::factory()->create(['created_at' => now()->subDay()]);
    $newest = Product::factory()->create(['slug' => 'newest-oud']);

    $reply = $this->postJson('/api/v1/chat', ['message' => 'what are the newest perfumes?'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', 'search_products')
        ->assertJsonStructure(['data' => ['reply' => ['id', 'key', 'role', 'text', 'data' => ['*' => ['id', 'name', 'slug', 'price', 'images', 'primary_image', 'variants']], 'created_at']]])
        ->json('data.reply');

    expect(array_keys($reply))->toBe(['id', 'key', 'role', 'text', 'data', 'created_at'])
        ->and(array_column($reply['data'], 'id'))->toBe([$newest->id, $older->id])
        ->and($reply['data'][0]['slug'])->toBe('newest-oud');
});

it('returns only the customer orders as order data', function (): void {
    $customer = User::factory()->create();
    Order::factory()->create(['user_id' => $customer->id, 'order_no' => 'MKR-1111']);
    Order::factory()->create(['order_no' => 'MKR-2222']);
    $this->actingAs($customer, 'sanctum');

    $this->postJson('/api/v1/chat', ['message' => 'where is my order?'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', 'get_my_orders')
        ->assertJsonCount(1, 'data.reply.data')
        ->assertJsonPath('data.reply.data.0.order_no', 'MKR-1111')
        ->assertJsonStructure(['data' => ['reply' => ['data' => ['*' => ['status', 'status_label', 'total', 'items', 'timeline']]]]]);
});

it('marks wishlisted products in the reply data', function (): void {
    $customer = User::factory()->create();
    $wishlist = Wishlist::factory()->create(['user_id' => $customer->id]);
    $this->actingAs($customer, 'sanctum');

    $this->postJson('/api/v1/chat', ['message' => 'show my wishlist'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', 'get_my_wishlist')
        ->assertJsonPath('data.reply.data.0.id', $wishlist->product_id)
        ->assertJsonPath('data.reply.data.0.is_wishlisted', true);
});

it('returns the key with empty data when a tool has nothing to show', function (): void {
    $this->postJson('/api/v1/chat', ['message' => 'where is my order?'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', 'get_my_orders')
        ->assertJsonPath('data.reply.data', []);
});

it('returns no key and no data when no tool was used', function (): void {
    $this->postJson('/api/v1/chat', ['message' => 'hello'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', null)
        ->assertJsonPath('data.reply.data', []);
});

it('returns single-answer tools as one object in data', function (): void {
    $this->postJson('/api/v1/chat', ['message' => 'how much is shipping?'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', 'get_store_info')
        ->assertJsonCount(1, 'data.reply.data')
        ->assertJsonStructure(['data' => ['reply' => ['data' => ['*' => ['currency', 'tax_rate_percent', 'shipping_fee', 'free_shipping_threshold', 'payment_methods']]]]]);
});

it('uses the last tool the model called as the key', function (): void {
    $product = Product::factory()->create();
    scriptChatbot([
        toolTurn('get_store_info'),
        toolTurn('search_products', ['limit' => 1]),
        textTurn('Here you go.'),
    ]);

    $this->postJson('/api/v1/chat', ['message' => 'Hi'])
        ->assertOk()
        ->assertJsonPath('data.reply.key', 'search_products')
        ->assertJsonPath('data.reply.data.0.id', $product->id);
});

it('keeps the key and data in the conversation history', function (): void {
    Product::factory()->create();

    $conversationId = $this->postJson('/api/v1/chat', ['message' => 'recommend a perfume'])->json('data.conversation_id');

    $this->getJson("/api/v1/chat/{$conversationId}")
        ->assertOk()
        ->assertJsonPath('data.messages.0.key', null)
        ->assertJsonPath('data.messages.0.data', [])
        ->assertJsonPath('data.messages.1.key', 'search_products')
        ->assertJsonCount(1, 'data.messages.1.data');
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
