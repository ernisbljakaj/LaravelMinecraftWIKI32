<?php

/*
|--------------------------------------------------------------------------
| Default Blocklist
|--------------------------------------------------------------------------
|
| An .env value is always a string, so the custom blocklist is supplied as a
| comma separated list. Leaving it empty keeps the defaults below.
|
*/

$customBlocklist = trim((string) env('COMMENT_MODERATION_BLOCKLIST', ''));

$blocklist = $customBlocklist === ''
    ? []
    : array_values(array_filter(array_map('trim', explode(',', $customBlocklist))));

return [

    /*
    |--------------------------------------------------------------------------
    | Comment Moderation Driver
    |--------------------------------------------------------------------------
    |
    | Which moderator reviews every newly submitted comment.
    |
    |   heuristic - pure rules, no network, no API key. The safe default.
    |   ai        - the AI moderator. Falls back to the heuristic moderator
    |               automatically when no API key is configured.
    |   auto      - the AI moderator when it is usable, otherwise heuristic.
    |   off       - no moderation; every comment is published immediately.
    |
    */

    'driver' => env('COMMENT_MODERATION_DRIVER', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Asynchronous Moderation
    |--------------------------------------------------------------------------
    |
    | When true the verdict runs on the queue so a slow AI call never blocks
    | the person who posted the comment. With it off, moderation runs inline
    | and the redirect can report the outcome immediately.
    |
    */

    'queue' => (bool) env('COMMENT_MODERATION_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Submission Quota
    |--------------------------------------------------------------------------
    |
    | How many comments one account may post per minute. Throttling happens
    | before moderation, so a flood of spam never reaches the moderator.
    |
    */

    'quota' => [
        'per_minute' => (int) env('COMMENT_QUOTA_PER_MINUTE', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Heuristic Moderator
    |--------------------------------------------------------------------------
    |
    | Rules applied by the heuristic moderator. The blocklist is matched after
    | leetspeak normalisation, so "sh1t" is caught by the entry "shit". The
    | links rules only reject outright link spam; a single link in an
    | otherwise normal comment is allowed through.
    |
    */

    'heuristic' => [
        'min_length' => (int) env('COMMENT_MODERATION_MIN_LENGTH', 3),
        'max_length' => (int) env('COMMENT_MODERATION_MAX_LENGTH', 2000),
        'max_links' => (int) env('COMMENT_MODERATION_MAX_LINKS', 2),
        'max_caps_ratio' => (float) env('COMMENT_MODERATION_MAX_CAPS_RATIO', 0.7),
        'max_repeated_chars' => (int) env('COMMENT_MODERATION_MAX_REPEATED_CHARS', 8),
        'duplicate_window_minutes' => (int) env('COMMENT_MODERATION_DUPLICATE_WINDOW', 60),
        'blocklist' => $blocklist !== [] ? $blocklist : [
            'fuck', 'shit', 'bitch', 'cunt', 'asshole', 'dick', 'whore', 'slut',
            'nigger', 'faggot', 'retard', 'kike', 'spic', 'tranny',
            'suicide', 'kill yourself', 'rape', 'terrorist',
            'hate you', 'idiot', 'moron', 'stupid',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Moderator
    |--------------------------------------------------------------------------
    |
    | Any OpenAI-compatible chat completions endpoint works, which covers
    | OpenAI, Groq, Together, Ollama, LM Studio and friends. The moderator
    | asks for a single JSON object back and treats anything it cannot parse
    | as "pending" so a malformed or failed response can never silently
    | delete somebody's comment.
    |
    */

    'ai' => [
        'key' => env('COMMENT_MODERATION_AI_KEY'),
        'base_url' => env('COMMENT_MODERATION_AI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('COMMENT_MODERATION_AI_MODEL', 'gpt-4o-mini'),
        'timeout' => (int) env('COMMENT_MODERATION_AI_TIMEOUT', 10),
        'max_tokens' => (int) env('COMMENT_MODERATION_AI_MAX_TOKENS', 200),
        'rejected_below_confidence' => (float) env('COMMENT_MODERATION_AI_REJECT_BELOW', 0.75),
        'approved_above_confidence' => (float) env('COMMENT_MODERATION_AI_APPROVE_ABOVE', 0.75),
    ],

];
