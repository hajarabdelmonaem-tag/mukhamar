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
        '/طلب|اوردر|أوردر|order/iu' => ['get_my_orders', []],
        '/سل[ةه]|cart/iu' => ['get_my_cart', []],
        '/شحن|توصيل|ضريب|دفع|shipping|delivery|tax|payment/iu' => ['get_store_info', []],
        '/خصوصي|privacy/iu' => ['get_policy', ['page' => 'privacy']],
        '/سياس|شروط|استرجاع|استبدال|terms|policy|return/iu' => ['get_policy', ['page' => 'terms']],
        '/قسم|اقسام|أقسام|categor/iu' => ['list_categories', []],
    ];

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
        [$tool, $input] = self::route($text);

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
     * Pick the tool and input for a customer message.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private static function route(string $text): array
    {
        foreach (self::ROUTES as $pattern => $route) {
            if (preg_match($pattern, $text)) {
                return $route;
            }
        }

        return ['search_products', self::productSearchInput($text)];
    }

    /**
     * Build search_products input from the wording of a product question:
     * sort order, a price cap, a fragrance note, and one or three results.
     *
     * @return array<string, mixed>
     */
    private static function productSearchInput(string $text): array
    {
        $text = strtr($text, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
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
     * Turn a tool result into a readable reply, the way the real model would
     * summarise it (without any AI, so the wording is fixed).
     *
     * @param  array<string, mixed>  $data
     */
    private static function format(string $tool, array $data): string
    {
        if (isset($data['error'])) {
            return in_array($tool, ['get_my_orders', 'get_order_details', 'get_my_cart'], true)
                && str_contains($data['error'], 'not logged in')
                ? __('api.chatbot.fake.login_required')
                : __('api.chatbot.fake.not_found');
        }

        $currency = $data['currency'] ?? '';

        return match ($tool) {
            'search_products' => $data['products'] === []
                ? __('api.chatbot.fake.no_products')
                : __('api.chatbot.fake.products_intro')."\n\n".collect($data['products'])
                    ->map(fn (array $product, int $index): string => ($index + 1).'. '.self::formatProduct($product, $currency))
                    ->implode("\n\n"),
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
