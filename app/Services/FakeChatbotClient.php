<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * A free, offline stand-in for Claude used while building and testing.
 *
 * It picks a tool from keywords in the customer's message, then echoes the
 * tool's real result back as the reply, so the whole flow (API, storage,
 * tools, database queries) can be tried without an API key or any cost.
 */
class FakeChatbotClient implements ChatbotClient
{
    /**
     * Keyword patterns mapped to the tool the fake bot calls for them.
     *
     * @var array<string, array{0: string, 1: array<string, mixed>}>
     */
    private const ROUTES = [
        '/مفضل|wishlist|favou?rite/iu' => ['get_my_wishlist', []],
        '/عنوان|عناوين|address/iu' => ['get_my_addresses', []],
        '/إشعار|اشعار|notification/iu' => ['get_my_notifications', []],
        '/حسابي|بياناتي|ملفي|my account|profile/iu' => ['get_my_account', []],
        '/طلب|اوردر|أوردر|order/iu' => ['get_my_orders', []],
        '/سل[ةه]|cart/iu' => ['get_my_cart', []],
        '/شحن|توصيل|ضريب|دفع|shipping|delivery|tax|payment/iu' => ['get_store_info', []],
        '/خصوصي|privacy/iu' => ['get_policy', ['page' => 'privacy']],
        '/سياس|شروط|استرجاع|استبدال|terms|policy|return/iu' => ['get_policy', ['page' => 'terms']],
        '/قسم|اقسام|أقسام|categor/iu' => ['list_categories', []],
        '/(?:الأ|الا|أ|ا)كثر مبيع|best ?sell|popular/iu' => ['get_best_sellers', []],
        '/عروض|(?<![اأم])عرض|خصم|تخفيض|offer|sale|discount/iu' => ['get_current_offers', []],
        '/تواصل|رقمكم|تليفون|تلفون|جوال|ايميل|إيميل|بريد|انستجرام|انستغرام|سوشيال|فيسبوك|تيك ?توك|contact|phone|email|instagram|social/iu' => ['get_contact_info', []],
    ];

    /**
     * Words that mean the customer is asking about products in general.
     */
    private const PRODUCT_PATTERN = '/عطر|عطور|منتج|برفان|بخور|دهن|perfume|fragrance|product|scent|bakhoor|incense|recommend|ترشيح|رشح/iu';

    /**
     * Words that mean the customer is asking about a coupon code.
     */
    private const COUPON_PATTERN = '/كوبون|كود|coupon|promo|voucher/iu';

    /**
     * Words that mean the customer wants a product's reviews.
     */
    private const REVIEWS_PATTERN = '/تقييمات|آراء|اراء|ريفيو|reviews?/iu';

    /**
     * Product search sort orders recognised in a customer message.
     *
     * @var array<string, string>
     */
    private const SORTS = [
        '/آخر|اخر|أحدث|احدث|جديد|latest|newest|new|recent/iu' => 'newest',
        '/أرخص|ارخص|cheapest|cheap|lowest price/iu' => 'price_asc',
        '/أغلى|اغلى|most expensive|highest price/iu' => 'price_desc',
        '/أفضل|افضل|أعلى تقييم|اعلى تقييم|best|top rated|highest rated/iu' => 'rating',
    ];

    /**
     * Fragrance notes recognised in a customer message, used as the search query.
     *
     * @var list<string>
     */
    private const NOTES = [
        'عود', 'ورد', 'مسك', 'عنبر', 'زعفران', 'هيل', 'فانيليا', 'فانيلا', 'ياسمين', 'جاسمين', 'باتشولي', 'برغموت', 'ليمون', 'صندل',
        'oud', 'rose', 'musk', 'amber', 'saffron', 'cardamom', 'vanilla', 'jasmine', 'patchouli', 'bergamot', 'lemon', 'sandalwood',
    ];

    /**
     * Send the conversation and return the fake model's next turn.
     *
     * @param  list<array{type: string, text: string}>  $system
     * @param  list<array<string, mixed>>  $tools
     * @param  list<array{role: string, content: string|list<array<string, mixed>>}>  $messages
     * @return array{content: list<array<string, mixed>>, stop_reason: ?string, input_tokens: int, output_tokens: int}
     */
    public function send(array $system, array $tools, array $messages, bool $allowTools = true): array
    {
        $last = end($messages);
        $blocks = is_array($last['content']) ? $last['content'] : [['type' => 'text', 'text' => $last['content']]];
        $toolResult = collect($blocks)->firstWhere('type', 'tool_result');

        if ($toolResult) {
            $data = json_decode((string) $toolResult['content'], true) ?? [];

            return self::reply(__('api.chatbot.fake.header')."\n\n".self::format(
                self::toolName($messages, $toolResult['tool_use_id']),
                $data,
            ));
        }

        if (! $allowTools) {
            return self::reply(__('api.chatbot.fake_reply'));
        }

        $text = (string) collect($blocks)->firstWhere('type', 'text')['text'];
        $route = self::route($text);

        if ($route === null) {
            return self::reply(__('api.chatbot.fake.header')."\n\n".__('api.chatbot.fake.help'));
        }

        [$tool, $input] = $route;

        return [
            'content' => [[
                'type' => 'tool_use',
                'id' => 'toolu_fake_'.Str::random(16),
                'name' => $tool,
                'input' => $input,
            ]],
            'stop_reason' => 'tool_use',
            'input_tokens' => 0,
            'output_tokens' => 0,
        ];
    }

    /**
     * Pick the tool and input for a customer message, or null when the
     * message is not a question the fake bot recognises (a greeting, "help").
     *
     * @return array{0: string, 1: array<string, mixed>}|null
     */
    private static function route(string $text): ?array
    {
        if (preg_match(self::COUPON_PATTERN, $text)) {
            $code = self::couponCode($text);

            return $code ? ['check_coupon', ['code' => $code]] : ['get_current_offers', []];
        }

        if (preg_match(self::REVIEWS_PATTERN, $text) && preg_match('/\d+/', self::westernDigits($text), $match)) {
            return ['get_product_reviews', ['product_id' => (int) $match[0]]];
        }

        foreach (self::ROUTES as $pattern => $route) {
            if (preg_match($pattern, $text)) {
                return $route;
            }
        }

        $input = self::productSearchInput($text);

        if (count($input) > 1 || preg_match(self::PRODUCT_PATTERN, $text)) {
            return ['search_products', $input];
        }

        return null;
    }

    /**
     * Build search_products input from the wording of a product question:
     * sort order, a price cap, a fragrance note, and one or three results.
     *
     * @return array<string, mixed>
     */
    private static function productSearchInput(string $text): array
    {
        $text = self::westernDigits($text);
        $input = ['limit' => 3];

        foreach (self::SORTS as $pattern => $sort) {
            if (preg_match($pattern, $text)) {
                $input['sort'] = $sort;
                break;
            }
        }

        if (preg_match('/(?:تحت|أقل من|اقل من|under|below|less than)\s*(\d+)/iu', $text, $match)) {
            $input['max_price'] = (int) $match[1];
        }

        foreach (self::NOTES as $note) {
            if (mb_stripos($text, $note) !== false) {
                $input['query'] = $note;
                break;
            }
        }

        if (isset($input['sort']) && preg_match('/(?:آخر|اخر|أحدث|احدث|أرخص|ارخص|أغلى|اغلى|أفضل|افضل)\s+(?:منتج|عطر)|(?:latest|newest|cheapest|best|most expensive)\s+(?:product|perfume)\b/iu', $text)) {
            $input['limit'] = 1;
        }

        return $input;
    }

    /**
     * Find a coupon-looking token (letters and digits, with a digit or in
     * capitals) in the message.
     */
    private static function couponCode(string $text): ?string
    {
        preg_match_all('/[A-Za-z0-9][A-Za-z0-9-]{2,19}/', self::westernDigits($text), $matches);

        return collect($matches[0])
            ->reject(fn (string $token): bool => in_array(strtolower($token), ['coupon', 'promo', 'code', 'voucher'], true))
            ->first(fn (string $token): bool => preg_match('/\d/', $token) === 1 || strtoupper($token) === $token);
    }

    /**
     * Convert Arabic-Indic digits to 0-9.
     */
    private static function westernDigits(string $text): string
    {
        return strtr($text, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
    }

    /**
     * Turn a tool result into a readable reply, the way the real model would
     * summarise it (without any AI, so the wording is fixed).
     *
     * @param  array<string, mixed>  $data
     */
    private static function format(string $tool, array $data): string
    {
        if (isset($data['error'])) {
            return str_contains($data['error'], 'not logged in')
                ? __('api.chatbot.fake.login_required')
                : __('api.chatbot.fake.not_found');
        }

        $currency = $data['currency'] ?? '';

        return match ($tool) {
            'search_products', 'get_best_sellers' => self::formatProductList($data['products'], $currency),
            'get_my_wishlist' => $data['products'] === []
                ? __('api.chatbot.fake.wishlist_empty')
                : self::formatProductList($data['products'], $currency),
            'get_current_offers' => self::formatOffers($data, $currency),
            'check_coupon' => self::formatCoupon($data, $currency),
            'get_product_reviews' => implode("\n", [
                __('api.chatbot.fake.reviews_intro', [
                    'product' => $data['product_name'],
                    'rating' => $data['average_rating'],
                    'count' => $data['reviews_count'],
                ]),
                ...($data['reviews'] === []
                    ? [__('api.chatbot.fake.no_reviews')]
                    : collect($data['reviews'])
                        ->map(fn (array $review): string => "• {$review['rating']}/5".($review['title'] ? " - {$review['title']}" : '').': '.Str::limit((string) $review['body'], 120))
                        ->all()),
            ]),
            'get_contact_info' => implode("\n", array_filter([
                $data['email'] ? __('api.chatbot.fake.contact_email', ['email' => $data['email']]) : null,
                $data['phone'] ? __('api.chatbot.fake.contact_phone', ['phone' => $data['phone']]) : null,
                ...collect($data['socials'])->map(fn (array $social): string => "• {$social['name']}: {$social['link']}")->all(),
            ])),
            'get_my_addresses' => $data['addresses'] === []
                ? __('api.chatbot.fake.no_addresses')
                : collect($data['addresses'])
                    ->map(fn (array $address): string => '• '.implode(__('api.chatbot.fake.separator'), array_filter([$address['street'], $address['district'], $address['city']]))
                        .($address['is_default'] ? ' ('.__('api.chatbot.fake.default_address').')' : ''))
                    ->implode("\n"),
            'get_my_notifications' => implode("\n", [
                __('api.chatbot.fake.unread_notifications', ['count' => $data['unread_count']]),
                ...collect($data['notifications'])
                    ->map(fn (array $notification): string => '• '.($notification['is_read'] ? '' : '('.__('api.chatbot.fake.new_mark').') ')."{$notification['title']}: {$notification['body']}")
                    ->all(),
            ]),
            'get_my_account' => implode("\n", array_filter([
                __('api.chatbot.fake.account_name', ['name' => $data['name']]),
                $data['email'] ? __('api.chatbot.fake.contact_email', ['email' => $data['email']]) : null,
                $data['phone'] ? __('api.chatbot.fake.contact_phone', ['phone' => $data['phone']]) : null,
                __('api.chatbot.fake.account_orders', ['count' => $data['orders_count']]),
            ])),
            'get_product_details' => self::formatProduct($data, $currency)
                .($data['description'] ? "\n\n".Str::limit(strip_tags((string) $data['description']), 300) : ''),
            'list_categories' => __('api.chatbot.fake.categories_intro')."\n".collect($data['categories'])
                ->map(fn (array $category): string => '• '.$category['name'])
                ->implode("\n"),
            'get_store_info' => implode("\n", [
                __('api.chatbot.fake.shipping', [
                    'fee' => $data['shipping_fee'],
                    'threshold' => $data['free_shipping_threshold'],
                    'currency' => $currency,
                ]),
                __('api.chatbot.fake.tax', ['rate' => $data['tax_rate_percent']]),
                __('api.chatbot.fake.payment_methods', ['methods' => implode(__('api.chatbot.fake.separator'), $data['payment_methods'])]),
            ]),
            'get_policy' => trim(($data['title'] ?? '')."\n\n".collect($data['sections'] ?? [])
                ->map(fn (array $section): string => trim(($section['heading'] ?? '')."\n".Str::limit(strip_tags((string) ($section['body'] ?? '')), 200)))
                ->take(3)
                ->implode("\n\n")),
            'get_my_orders' => $data['orders'] === []
                ? __('api.chatbot.fake.no_orders')
                : __('api.chatbot.fake.orders_intro')."\n".collect($data['orders'])
                    ->map(fn (array $order): string => "• {$order['order_no']}: {$order['status_label']} - {$order['total']} {$currency} ({$order['placed_at']})")
                    ->implode("\n"),
            'get_order_details' => implode("\n", [
                __('api.chatbot.fake.order_status', ['order_no' => $data['order_no'], 'status' => $data['status_label']]),
                __('api.chatbot.fake.order_total', ['total' => $data['total'], 'currency' => $currency]),
                ...collect($data['items'])->map(fn (array $item): string => "• {$item['product_name']} × {$item['quantity']}")->all(),
            ]),
            'get_my_cart' => $data['items'] === []
                ? __('api.chatbot.fake.cart_empty')
                : collect($data['items'])
                    ->map(fn (array $item): string => "• {$item['product_name']} × {$item['quantity']} = {$item['line_total']} {$data['summary']['currency']}")
                    ->push(__('api.chatbot.fake.order_total', ['total' => $data['summary']['total'], 'currency' => $data['summary']['currency']]))
                    ->implode("\n"),
            'contact_support' => __('api.chatbot.fake.support_sent'),
            default => __('api.chatbot.fake_reply'),
        };
    }

    /**
     * A numbered list of products, or a "nothing found" line.
     *
     * @param  list<array<string, mixed>>  $products
     */
    private static function formatProductList(array $products, string $currency): string
    {
        if ($products === []) {
            return __('api.chatbot.fake.no_products');
        }

        return __('api.chatbot.fake.products_intro')."\n\n".collect($products)
            ->map(fn (array $product, int $index): string => ($index + 1).'. '.self::formatProduct($product, $currency))
            ->implode("\n\n");
    }

    /**
     * Banners and discounted products.
     *
     * @param  array<string, mixed>  $data
     */
    private static function formatOffers(array $data, string $currency): string
    {
        if ($data['banners'] === [] && $data['products_on_sale'] === []) {
            return __('api.chatbot.fake.no_offers');
        }

        $lines = collect($data['banners'])
            ->map(fn (array $banner): string => '• '.trim($banner['title'].($banner['description'] ? ': '.Str::limit(strip_tags((string) $banner['description']), 100) : '')));

        if ($data['products_on_sale'] !== []) {
            $lines->push('', __('api.chatbot.fake.on_sale_intro'));
            $lines = $lines->merge(collect($data['products_on_sale'])
                ->map(fn (array $product): string => "• {$product['name']}: {$product['price']} {$currency} "
                    .__('api.chatbot.fake.was', ['price' => "{$product['old_price']} {$currency}"])
                    ." -{$product['discount_percent']}%"));
        }

        return trim($lines->implode("\n"));
    }

    /**
     * Whether a coupon works and what it gives.
     *
     * @param  array<string, mixed>  $data
     */
    private static function formatCoupon(array $data, string $currency): string
    {
        if (! $data['valid']) {
            return __("api.chatbot.fake.coupon_invalid.{$data['reason']}", [
                'code' => $data['code'],
                'min' => ($data['min_order_amount'] ?? '')." {$currency}",
            ]);
        }

        $discount = $data['discount_type'] === 'percentage'
            ? "{$data['discount_value']}%"
            : "{$data['discount_value']} {$currency}";

        return implode("\n", array_filter([
            __('api.chatbot.fake.coupon_valid', ['code' => $data['code'], 'discount' => $discount]),
            $data['min_order_amount'] ? __('api.chatbot.fake.coupon_min', ['min' => "{$data['min_order_amount']} {$currency}"]) : null,
            $data['expires_at'] ? __('api.chatbot.fake.coupon_expires', ['date' => $data['expires_at']]) : null,
        ]));
    }

    /**
     * One product as a few short lines.
     *
     * @param  array<string, mixed>  $product
     */
    private static function formatProduct(array $product, string $currency): string
    {
        $price = "{$product['price']} {$currency}"
            .($product['old_price'] ? ' '.__('api.chatbot.fake.was', ['price' => "{$product['old_price']} {$currency}"]) : '');

        $lines = [
            $product['name'].($product['category'] ? " ({$product['category']})" : ''),
            '   '.__('api.chatbot.fake.price', ['price' => $price]),
            '   '.__('api.chatbot.fake.notes', ['notes' => implode(' · ', array_filter($product['notes']))]),
            '   '.__('api.chatbot.fake.rating', ['rating' => $product['rating'], 'count' => $product['reviews_count']]),
        ];

        if ($product['sizes'] !== []) {
            $lines[] = '   '.__('api.chatbot.fake.sizes', ['sizes' => collect($product['sizes'])
                ->map(fn (array $size): string => "{$size['name']}: {$size['price']} {$currency}"
                    .($size['in_stock'] ? '' : ' ('.__('api.chatbot.fake.out_of_stock').')'))
                ->implode(__('api.chatbot.fake.separator'))]);
        }

        return implode("\n", $lines);
    }

    /**
     * Find the tool name of the tool_use block a result answers.
     *
     * @param  list<array{role: string, content: string|list<array<string, mixed>>}>  $messages
     */
    private static function toolName(array $messages, string $toolUseId): string
    {
        foreach (array_reverse($messages) as $message) {
            foreach (is_array($message['content']) ? $message['content'] : [] as $block) {
                if (($block['type'] ?? null) === 'tool_use' && $block['id'] === $toolUseId) {
                    return $block['name'];
                }
            }
        }

        return 'unknown';
    }

    /**
     * Build a final text turn.
     *
     * @return array{content: list<array<string, mixed>>, stop_reason: ?string, input_tokens: int, output_tokens: int}
     */
    private static function reply(string $text): array
    {
        return [
            'content' => [['type' => 'text', 'text' => $text]],
            'stop_reason' => 'end_turn',
            'input_tokens' => 0,
            'output_tokens' => 0,
        ];
    }
}
