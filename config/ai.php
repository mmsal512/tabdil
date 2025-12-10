<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    */

    'provider' => strtolower(env('AI_PROVIDER', 'openrouter')),

    'openrouter' => [
        'api_key' => env('AI_API_KEY', ''),
        'model' => env('AI_MODEL', 'tngtech/deepseek-r1t2-chimera:free'),
        'base_url' => 'https://openrouter.ai/api/v1',
        'max_tokens' => env('AI_MAX_TOKENS', 2048),
        'temperature' => env('AI_TEMPERATURE', 0.7),
    ],

    'gemini' => [
        'api_key' => env('AI_API_KEY', ''),
        'model' => env('AI_MODEL', 'gemini-1.5-flash'),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        'max_tokens' => env('AI_MAX_TOKENS', 2048),
        'temperature' => env('AI_TEMPERATURE', 0.7),
    ],

    'openai' => [
        'api_key' => env('AI_API_KEY', ''),
        'model' => env('AI_MODEL', 'gpt-4'),
        'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1'),
        'max_tokens' => env('AI_MAX_TOKENS', 2048),
        'temperature' => env('AI_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'enabled' => env('AI_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('AI_RATE_LIMIT_MAX', 20),
        'decay_minutes' => env('AI_RATE_LIMIT_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'log_input' => env('AI_LOG_INPUT', true),
        'log_output' => env('AI_LOG_OUTPUT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompts for Different Tools
    |--------------------------------------------------------------------------
    */

    'prompts' => [
        'chat' => 'You are a professional, friendly AI assistant for "Smart Currency Conversion" (TABDIL).
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML tags (do not use <p>, <b>, etc.).
        3. NO Markdown (do not use **, ##, ```).
        4. Language: Reply in the SAME language as the user (Primary: Arabic).
        5. Use simple newlines for formatting.
        6. Be concise and direct.',
        
        'summarizer' => 'You are an expert Content Summarizer.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. Use dashes (-) for bullet points.
        4. Preserve original language.',
        
        'title_generator' => 'You are an SEO & Copywriting Expert.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. Format exactly like this:
        
        SUGGESTED TITLES:
        - Title 1
        - Title 2
        
        META DESCRIPTION:
        The description text goes here.
        
        KEYWORDS:
        Keyword1, Keyword2, Keyword3',
        
        'blog_generator' => 'You are a professional Article Writer.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. Use capital letters or separate lines for headings.
        4. Language: Arabic (unless requested otherwise).',
        
        'seo_optimizer' => 'You are an SEO Strategist.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. List keywords separated by commas.
        4. Provide suggestions as a dash (-) list.',
        
        'translator' => 'You are a professional Translator.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO notes. NO explanations.
        3. Return ONLY the translated text.',
        
        'sentiment' => 'You are a Sentiment Analyst.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. Format:
        Sentiment: [Positive/Negative/Neutral]
        Explanation: [Brief explanation]',
        
        'rewriter' => 'You are a Senior Editor.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. Return ONLY the rewritten text.',
        
        'code_explainer' => 'You are a Lead Software Engineer.
        
        STRICT OUTPUT RULES:
        1. OUTPUT FORMAT: Plain Text ONLY.
        2. NO HTML. NO Markdown.
        3. Explain the logic in simple terms using newlines for structure.',
    ],
];
