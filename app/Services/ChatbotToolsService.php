<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * The store data the chatbot can look up. Every tool reads through the
 * existing models; customer data is always scoped to the authenticated
 * user passed in by the server, never to an id chosen by the model.
 */
class ChatbotToolsService
{
    public function __construct(
        private SettingsService $settings,
        private PricingService $pricing,
    ) {}

    /**
     * The tool definitions sent to the model, in a fixed order so the
     * prompt prefix stays cacheable.
     *
     * @return list<array<string, mixed>>
     */
    public function definitions(): array
    {
        return [
            [
                'name' => 'search_products',
                'description' => 'Search the active perfumes in the store. Matches the query against product names, descriptions and fragrance notes (top, heart, base) in Arabic and English. Call with no query to list featured or newest products. Returns prices, sizes and stock.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'A short keyword such as a note (oud, rose, musk), a product name or a style. Use one or two words, not a full sentence.'],
                        'category_id' => ['type' => 'integer', 'description' => 'Limit results to a category id from list_categories.'],
                        'min_price' => ['type' => 'number', 'description' => 'Minimum base price.'],
                        'max_price' => ['type' => 'number', 'description' => 'Maximum base price.'],
                        'sort' => ['type' => 'string', 'enum' => ['price_asc', 'price_desc', 'rating', 'newest'], 'description' => 'Use newest for the latest added products. Without a sort, featured products come first.'],
                        'limit' => ['type' => 'integer', 'description' => 'Number of results, 1 to 10. Defaults to 5.'],
                    ],
                ],
            ],
            [
                'name' => 'get_product_details',
                'description' => 'Get the full details of one product: description, usage instructions, fragrance notes, every size with its price and stock, and recent approved reviews.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'product_id' => ['type' => 'integer', 'description' => 'The product id returned by search_products.'],
                    ],
                    'required' => ['product_id'],
                ],
            ],
            [
                'name' => 'list_categories',
                'description' => 'List the active product categories with their ids.',
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass],
            ],
            [
                'name' => 'get_store_info',
                'description' => 'Get the store currency, tax rate, shipping fee, the order amount above which shipping is free, and the supported payment methods.',
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass],
            ],
            [
                'name' => 'get_policy',
                'description' => 'Get the text of a store policy page.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'string', 'enum' => ['terms', 'privacy'], 'description' => 'terms covers terms and conditions (including returns if the store lists them there); privacy is the privacy policy.'],
                    ],
                    'required' => ['page'],
                ],
            ],
            [
                'name' => 'get_my_orders',
                'description' => "List the logged-in customer's most recent orders with status and totals. Only works for logged-in customers.",
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'description' => 'Number of orders, 1 to 10. Defaults to 5.'],
                    ],
                ],
            ],
            [
                'name' => 'get_order_details',
                'description' => "Get one of the logged-in customer's orders by its order number: status, timeline dates, items and totals.",
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'order_no' => ['type' => 'string', 'description' => 'The order number, for example MKR-1234.'],
                    ],
                    'required' => ['order_no'],
                ],
            ],
            [
                'name' => 'get_my_cart',
                'description' => "Get the logged-in customer's cart items with the price summary (subtotal, discount, shipping, tax, total, amount remaining for free shipping).",
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass],
            ],
            [
                'name' => 'contact_support',
                'description' => 'Send a message to the store support team on behalf of the customer. Only call this after the customer has confirmed they want it sent and what it should say.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'message' => ['type' => 'string', 'description' => "The customer's request, written clearly for the support team."],
                        'subject' => ['type' => 'string', 'description' => 'A short subject line.'],
                        'name' => ['type' => 'string', 'description' => "The guest's name. Not needed for logged-in customers."],
                        'phone' => ['type' => 'string', 'description' => "The guest's phone number so the team can reply. Not needed for logged-in customers."],
                    ],
                    'required' => ['message'],
                ],
            ],
        ];
    }

    /**
     * Run a tool and return its result payload.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function execute(string $name, array $input, ?User $user): array
    {
        return match ($name) {
            'search_products' => $this->searchProducts($input),
            'get_product_details' => $this->productDetails((int) ($input['product_id'] ?? 0)),
            'list_categories' => $this->categories(),
            'get_store_info' => $this->storeInfo(),
            'get_policy' => $this->policy((string) ($input['page'] ?? 'terms')),
            'get_my_orders' => $user ? $this->orders($user, $input) : self::loginRequired(),
            'get_order_details' => $user ? $this->orderDetails($user, (string) ($input['order_no'] ?? '')) : self::loginRequired(),
            'get_my_cart' => $user ? $this->cart($user) : self::loginRequired(),
            'contact_support' => $this->contactSupport($input, $user),
            default => ['error' => "Unknown tool: {$name}"],
        };
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function searchProducts(array $input): array
    {
        $query = trim((string) ($input['query'] ?? ''));
        $limit = max(1, min(10, (int) ($input['limit'] ?? 5)));

        $products = Product::query()
            ->with(['category', 'variants'])
            ->where('is_active', true)
            ->when($query !== '', fn (Builder $builder) => $builder->where(function (Builder $builder) use ($query): void {
                foreach (['name', 'description', 'top_notes', 'heart_notes', 'base_notes'] as $column) {
                    foreach (['ar', 'en'] as $locale) {
                        $builder->orWhere("{$column}->{$locale}", 'like', "%{$query}%");
                    }
                }
            }))
            ->when($input['category_id'] ?? null, fn (Builder $builder, $id) => $builder->where('category_id', $id))
            ->when($input['min_price'] ?? null, fn (Builder $builder, $min) => $builder->where('price', '>=', $min))
            ->when($input['max_price'] ?? null, fn (Builder $builder, $max) => $builder->where('price', '<=', $max))
            ->when(
                $input['sort'] ?? null,
                fn (Builder $builder, $sort) => match ($sort) {
                    'price_asc' => $builder->orderBy('price'),
                    'price_desc' => $builder->orderByDesc('price'),
                    'rating' => $builder->orderByDesc('rating'),
                    default => $builder->latest(),
                },
                fn (Builder $builder) => $builder->orderByDesc('is_featured')->latest(),
            )
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            'currency' => $this->settings->currency(),
            'count' => $products->count(),
            'products' => $products->map(fn (Product $product): array => $this->productSummary($product))->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function productDetails(int $productId): array
    {
        $product = Product::query()
            ->with([
                'category',
                'variants',
                'reviews' => fn ($query) => $query->where('is_approved', true)->latest()->limit(3),
            ])
            ->where('is_active', true)
            ->find($productId);

        if (! $product) {
            return ['error' => 'Product not found.'];
        }

        return [
            'currency' => $this->settings->currency(),
            ...$this->productSummary($product),
            'description' => $product->description,
            'usage_instructions' => $product->usage_instructions,
            'badges' => $product->badges,
            'recent_reviews' => $product->reviews->map(fn ($review): array => [
                'rating' => $review->rating,
                'title' => $review->title,
                'body' => $review->body,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function productSummary(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category?->name,
            'price' => (float) $product->price,
            'old_price' => $product->old_price !== null ? (float) $product->old_price : null,
            'rating' => (float) $product->rating,
            'reviews_count' => (int) $product->reviews_count,
            'notes' => [
                'top' => $product->top_notes,
                'heart' => $product->heart_notes,
                'base' => $product->base_notes,
            ],
            'sizes' => $product->variants
                ->where('is_active', true)
                ->map(fn (ProductVariant $variant): array => [
                    'name' => $variant->name,
                    'unit' => $variant->unit,
                    'price' => round((float) $product->price + (float) $variant->price_adjustment, 2),
                    'in_stock' => $variant->stock > 0,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function categories(): array
    {
        return [
            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Category $category): array => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_id' => $category->parent_id,
                ])
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function storeInfo(): array
    {
        return [
            'currency' => $this->settings->currency(),
            'tax_rate_percent' => $this->settings->taxRate(),
            'shipping_fee' => $this->settings->shippingFee(),
            'free_shipping_threshold' => $this->settings->freeShippingThreshold(),
            'payment_methods' => PaymentMethod::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (PaymentMethod $method): string => (string) $method->name)
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function policy(string $page): array
    {
        [$key, $legacyKey] = $page === 'privacy' ? ['privacy_policy', 'policy'] : ['terms_conditions', 'terms'];

        $document = Setting::query()->where('key', $key)->value('value')
            ?? Setting::query()->where('key', $legacyKey)->value('value');

        if (! $document) {
            return ['error' => 'This page has not been published by the store.'];
        }

        $localize = fn (array $value): ?string => $value[app()->getLocale()] ?? $value['en'] ?? $value['ar'] ?? null;

        return [
            'title' => $localize($document['title'] ?? []),
            'sections' => collect($document['sections'] ?? [])->map(fn (array $section): array => [
                'heading' => $localize($section['heading'] ?? []),
                'body' => $localize($section['body'] ?? []),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function orders(User $user, array $input): array
    {
        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->limit(max(1, min(10, (int) ($input['limit'] ?? 5))))
            ->get();

        return [
            'currency' => $this->settings->currency(),
            'orders' => $orders->map(fn (Order $order): array => [
                'order_no' => $order->order_no,
                'status' => $order->status,
                'status_label' => __("api.order.status.{$order->status}"),
                'total' => (float) $order->total,
                'items_count' => $order->items->sum('quantity'),
                'placed_at' => $order->placed_at?->format('Y-m-d'),
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function orderDetails(User $user, string $orderNo): array
    {
        $order = $user->orders()
            ->with(['items', 'address'])
            ->where('order_no', trim($orderNo))
            ->first();

        if (! $order) {
            return ['error' => 'No order with this number was found for this customer.'];
        }

        return [
            'currency' => $this->settings->currency(),
            'order_no' => $order->order_no,
            'status' => $order->status,
            'status_label' => __("api.order.status.{$order->status}"),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount,
            'shipping' => (float) $order->shipping,
            'tax' => (float) $order->tax,
            'total' => (float) $order->total,
            'placed_at' => $order->placed_at?->format('Y-m-d H:i'),
            'confirmed_at' => $order->confirmed_at?->format('Y-m-d H:i'),
            'shipped_at' => $order->shipped_at?->format('Y-m-d H:i'),
            'delivered_at' => $order->delivered_at?->format('Y-m-d H:i'),
            'cancelled_at' => $order->cancelled_at?->format('Y-m-d H:i'),
            'delivery_city' => $order->address?->city,
            'items' => $order->items->map(fn ($item): array => [
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'total' => (float) $item->total,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cart(User $user): array
    {
        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->with(['items.product', 'items.variant', 'coupon'])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return ['items' => [], 'message' => 'The cart is empty.'];
        }

        return [
            'items' => $cart->items->map(fn ($item): array => [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'variant_name' => $item->variant?->name,
                'quantity' => $item->quantity,
                'line_total' => $item->lineTotal(),
            ])->all(),
            'coupon' => $cart->coupon?->code,
            'summary' => $this->pricing->summary(
                $cart->items->sum(fn ($item) => $item->lineTotal()),
                $cart->coupon,
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function contactSupport(array $input, ?User $user): array
    {
        $message = trim((string) ($input['message'] ?? ''));

        if ($message === '') {
            return ['error' => 'The message is empty.'];
        }

        $phone = $user?->phone ?? ($input['phone'] ?? null);

        if (! $user && ! $phone) {
            return ['error' => 'Ask the guest for a phone number first so the team can reply.'];
        }

        ContactMessage::query()->create([
            'name' => $user?->name ?? ($input['name'] ?? null),
            'email' => $user?->email,
            'phone' => $phone ? mb_substr((string) $phone, 0, 20) : null,
            'subject' => mb_substr((string) ($input['subject'] ?? __('api.chatbot.support_subject')), 0, 255),
            'message' => mb_substr($message, 0, 5000),
        ]);

        return ['sent' => true];
    }

    /**
     * @return array<string, string>
     */
    private static function loginRequired(): array
    {
        return ['error' => 'The customer is not logged in. Ask them to log in to the app to see their orders or cart.'];
    }
}
