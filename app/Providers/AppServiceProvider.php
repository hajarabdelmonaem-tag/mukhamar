<?php

namespace App\Providers;

use Anthropic\Client;
use App\Services\AnthropicChatbotClient;
use App\Services\ChatbotClient;
use App\Services\FakeChatbotClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChatbotClient::class, function (): ChatbotClient {
            if (config('services.chatbot.driver') !== 'claude') {
                return new FakeChatbotClient;
            }

            return new AnthropicChatbotClient(new Client(
                apiKey: (string) config('services.anthropic.key'),
                requestOptions: ['timeout' => config('services.anthropic.timeout')],
            ));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('chatbot', fn (Request $request): Limit => Limit::perMinute(
            config('services.chatbot.rate_limit_per_minute'),
        )->by($request->user('sanctum')?->id ?: $request->ip()));
    }
}
