<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AiRequestLog;
use Illuminate\Support\Facades\Auth;

class AiService
{
    protected string $provider;
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $maxTokens;
    protected float $temperature;

    public function __construct()
    {
        // Fix: Use correct config key 'ai.provider' instead of 'ai.default'
        $this->provider = config('ai.provider', 'openrouter');
        
        if ($this->provider === 'openrouter') {
            $this->apiKey = config('ai.openrouter.api_key', '');
            $this->model = config('ai.openrouter.model', 'google/gemini-2.0-flash-exp:free');
            $this->baseUrl = config('ai.openrouter.base_url', 'https://openrouter.ai/api/v1');
            $this->maxTokens = (int) config('ai.openrouter.max_tokens', 2048);
            $this->temperature = (float) config('ai.openrouter.temperature', 0.7);
        } elseif ($this->provider === 'gemini') {
            $this->apiKey = config('ai.gemini.api_key', '');
            $this->model = config('ai.gemini.model', 'gemini-1.5-flash');
            $this->baseUrl = config('ai.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
            $this->maxTokens = (int) config('ai.gemini.max_tokens', 2048);
            $this->temperature = (float) config('ai.gemini.temperature', 0.7);
        } else {
            $this->apiKey = config('ai.openai.api_key', '');
            $this->model = config('ai.openai.model', 'gpt-4');
            $this->baseUrl = config('ai.openai.base_url', 'https://api.openai.com/v1');
            $this->maxTokens = (int) config('ai.openai.max_tokens', 2048);
            $this->temperature = (float) config('ai.openai.temperature', 0.7);
        }
    }

    /**
     * Send a request to AI API
     */
    public function chat(string $message, ?string $systemPrompt = null, array $options = []): array
    {
        if ($this->provider === 'gemini') {
            return $this->chatWithGemini($message, $systemPrompt, $options);
        }
        
        // OpenRouter and OpenAI use the same API format
        return $this->chatWithOpenAI($message, $systemPrompt, $options);
    }

    /**
     * Chat with Gemini API
     */
    protected function chatWithGemini(string $message, ?string $systemPrompt = null, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            $model = $options['model'] ?? $this->model;
            $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";

            $contents = [];
            
            if ($systemPrompt) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $systemPrompt]]
                ];
                $contents[] = [
                    'role' => 'model',
                    'parts' => [['text' => 'Understood. I will follow these instructions.']]
                ];
            }
            
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];

            $response = Http::timeout(60)->post($url, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? $this->temperature,
                    'maxOutputTokens' => $options['max_tokens'] ?? $this->maxTokens,
                ],
            ]);

            $executionTime = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return [
                        'success' => false,
                        'error' => $data['error']['message'] ?? 'No response from Gemini',
                        'execution_time' => $executionTime,
                    ];
                }
                
                $output = $data['candidates'][0]['content']['parts'][0]['text'];
                $tokens = $data['usageMetadata']['totalTokenCount'] ?? 0;

                $this->logRequest($message, $output, $tokens);

                return [
                    'success' => true,
                    'output' => $output,
                    'tokens' => $tokens,
                    'execution_time' => $executionTime,
                ];
            }

            $errorData = $response->json();
            return [
                'success' => false,
                'error' => $errorData['error']['message'] ?? 'Unknown error from Gemini API',
                'execution_time' => $executionTime,
            ];
        } catch (\Exception $e) {
            Log::error('Gemini AI Service Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'execution_time' => round((microtime(true) - $startTime) * 1000),
            ];
        }
    }

    /**
     * Chat with OpenAI/OpenRouter API
     */
    protected function chatWithOpenAI(string $message, ?string $systemPrompt = null, array $options = []): array
    {
        $startTime = microtime(true);
        
        // Force Fresh Config Reading (Fix for Laravel Cloud Caching)
        $isOpenRouter = (config('ai.provider') === 'openrouter');
        
        if ($isOpenRouter) {
            $apiKey = config('ai.openrouter.api_key');
            $baseUrl = config('ai.openrouter.base_url', 'https://openrouter.ai/api/v1');
            $model = config('ai.openrouter.model', 'google/gemini-2.0-flash-exp:free');
        } else {
            $apiKey = config('ai.openai.api_key');
            $baseUrl = config('ai.openai.base_url');
            $model = config('ai.openai.model');
        }

        try {
            $messages = [];
            
            if ($systemPrompt) {
                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
            }
            
            $messages[] = ['role' => 'user', 'content' => $message];

            $headers = [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ];

            // Add OpenRouter specific headers
            if ($isOpenRouter) {
                $headers['HTTP-Referer'] = config('app.url', 'http://localhost');
                $headers['X-Title'] = config('app.name', 'TABDIL');
            }

            // Url Construction
            $url = rtrim($baseUrl, '/') . '/chat/completions';

            $payload = [
                'model' => $options['model'] ?? $model,
                'messages' => $messages,
            ];

            // Add params only if NOT using OpenRouter Free (to avoid errors)
            if (!$isOpenRouter) {
                 $payload['max_tokens'] = $options['max_tokens'] ?? 2048;
            }

            $response = Http::withHeaders($headers)
                ->timeout(60)
                ->post($url, $payload);

            $executionTime = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $output = $data['choices'][0]['message']['content'] ?? '';
                
                if (is_null($output)) $output = '';

                $tokens = $data['usage']['total_tokens'] ?? 0;

                $this->logRequest($message, $output, $tokens);

                return [
                    'success' => true,
                    'output' => $output,
                    'tokens' => $tokens,
                    'execution_time' => $executionTime,
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? $response->body();

            return [
                'success' => false,
                'error' => 'Provider Error (' . $response->status() . '): ' . $errorMessage,
                'execution_time' => $executionTime,
            ];
        } catch (\Exception $e) {
            Log::error('AI Service Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'execution_time' => round((microtime(true) - $startTime) * 1000),
            ];
        }
    }

    /**
     * Summarize text
     */
    public function summarize(string $text, string $language = 'en'): array
    {
        $prompt = $language === 'ar' 
            ? "لخص النص التالي باللغة العربية:\n\n{$text}"
            : "Summarize the following text:\n\n{$text}";
            
        return $this->chat($prompt, config('ai.prompts.summarizer'));
    }

    /**
     * Generate title and description
     */
    public function generateTitleDescription(string $content, string $language = 'en'): array
    {
        $prompt = $language === 'ar'
            ? "اقترح عنوانًا جذابًا ووصفًا ميتا (أقل من 160 حرفًا) للمحتوى التالي:\n\n{$content}"
            : "Generate an engaging title and meta description (under 160 chars) for:\n\n{$content}";
            
        return $this->chat($prompt, config('ai.prompts.title_generator'));
    }

    /**
     * Generate blog/article
     */
    public function generateBlog(string $topic, string $language = 'en', int $wordCount = 500): array
    {
        $prompt = $language === 'ar'
            ? "اكتب مقالة احترافية حول: {$topic}\nعدد الكلمات المطلوب: {$wordCount} كلمة تقريبًا"
            : "Write a professional blog post about: {$topic}\nTarget word count: approximately {$wordCount} words";
            
        return $this->chat($prompt, config('ai.prompts.blog_generator'), ['max_tokens' => 3000]);
    }

    /**
     * Generate SEO keywords
     */
    public function generateSeoKeywords(string $content, string $language = 'en'): array
    {
        $prompt = $language === 'ar'
            ? "اقترح 10 كلمات مفتاحية SEO للمحتوى التالي:\n\n{$content}"
            : "Suggest 10 SEO keywords for the following content:\n\n{$content}";
            
        return $this->chat($prompt, config('ai.prompts.seo_optimizer'));
    }

    /**
     * Translate text
     */
    public function translate(string $text, string $targetLanguage = 'ar'): array
    {
        $langName = $targetLanguage === 'ar' ? 'Arabic' : 'English';
        $prompt = "Translate the following text to {$langName}:\n\n{$text}";
            
        return $this->chat($prompt, config('ai.prompts.translator'));
    }

    /**
     * Analyze sentiment
     */
    public function analyzeSentiment(string $text): array
    {
        $prompt = "Analyze the sentiment of this text and respond with JSON format {\"sentiment\": \"positive/negative/neutral\", \"score\": 0.0-1.0, \"explanation\": \"...\"}:\n\n{$text}";
            
        return $this->chat($prompt, config('ai.prompts.sentiment'));
    }

    /**
     * Rewrite content
     */
    public function rewrite(string $text, string $style = 'professional'): array
    {
        $prompt = "Rewrite the following text in a {$style} style:\n\n{$text}";
            
        return $this->chat($prompt, config('ai.prompts.rewriter'));
    }

    /**
     * Explain code
     */
    public function explainCode(string $code, string $language = 'en'): array
    {
        $prompt = $language === 'ar'
            ? "اشرح الكود التالي بالعربية:\n\n```\n{$code}\n```"
            : "Explain the following code:\n\n```\n{$code}\n```";
            
        return $this->chat($prompt, config('ai.prompts.code_explainer'));
    }

    /**
     * Custom prompt
     */
    public function runCustomPrompt(string $prompt, ?string $systemPrompt = null): array
    {
        return $this->chat($prompt, $systemPrompt);
    }

    /**
     * Log AI request
     */
    protected function logRequest(string $input, string $output, int $tokens): void
    {
        if (!config('ai.logging.enabled')) {
            return;
        }

        try {
            AiRequestLog::create([
                'user_id' => Auth::id(),
                'input_text' => config('ai.logging.log_input') ? $input : null,
                'output_text' => config('ai.logging.log_output') ? $output : null,
                'tokens' => $tokens,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log AI request: ' . $e->getMessage());
        }
    }
}
