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
        'chat' => 'أنت مساعد ذكي لموقع "تبديل" (TABDIL) المتخصص في تحويل العملات.
        مهمتك هي مساعدة المستخدمين في أسئلة تحويل العملات وأسعار الصرف.
        
        القواعد الصارمة:
        1. يجب أن يكون ردك دائماً بنفس لغة المستخدم. إذا تحدث بالعربية، رد بالعربية فقط.
        2. كن ودوداً ومحترفاً ومختصراً.
        3. إذا سألك عن أسعار صرف، أخبره أنك مساعد ذكي وأن الأسعار الحالية موجودة في الجدول على الشاشة.
        4. لا تخرج عن سياق العملات والمالية.',
        
        'summarizer' => 'You are a text summarization expert. Summarize the given text concisely while maintaining the key points. Keep the summary clear and well-structured.',
        
        'title_generator' => 'You are an expert at creating compelling titles and meta descriptions. Generate SEO-friendly titles and descriptions for the given content.',
        
        'blog_generator' => 'You are a professional content writer. Create engaging, well-structured blog posts on the given topic. Include headers, bullet points, and a clear conclusion.',
        
        'seo_optimizer' => 'You are an SEO expert. Analyze the given content and suggest improvements for better search engine rankings. Provide keyword suggestions and optimization tips.',
        
        'translator' => 'You are a professional translator. Translate the given text accurately while maintaining the original tone and meaning. Support Arabic and English translation.',
        
        'sentiment' => 'You are a sentiment analysis expert. Analyze the given text and determine its sentiment (positive, negative, or neutral). Provide a brief explanation.',
        
        'rewriter' => 'You are a content rewriting expert. Rewrite the given text to make it more engaging, clear, and professional while maintaining the original meaning.',
        
        'code_explainer' => 'You are a programming expert. Explain the given code in simple terms. Break down complex concepts and provide examples where helpful.',
    ],
];
