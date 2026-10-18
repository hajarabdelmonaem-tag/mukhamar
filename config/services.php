<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-opus-5-5'),
        'effort' => env('ANTHROPIC_EFFORT', 'low'),
        'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 8000),
        'timeout' => (float) env('ANTHROPIC_TIMEOUT', 60),
    ],

    'chatbot' => [
        'enabled' => (bool) env('CHATBOT_ENABLED', true),
        'driver' => env('CHATBOT_DRIVER', 'fake'),
        'store_name' => env('CHATBOT_STORE_NAME', 'Mukhamar'),
        // Prefix for page links. Empty keeps relative app paths like /cart
        // (same as notification action_url); set to mukhamar:// or a website url if needed.
        'link_base' => env('CHATBOT_LINK_BASE', ''),
        'max_message_length' => (int) env('CHATBOT_MAX_MESSAGE_LENGTH', 1000),
        'max_turns' => (int) env('CHATBOT_MAX_TURNS', 30),
        'max_tool_rounds' => (int) env('CHATBOT_MAX_TOOL_ROUNDS', 5),
        'rate_limit_per_minute' => (int) env('CHATBOT_RATE_LIMIT_PER_MINUTE', 10),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
