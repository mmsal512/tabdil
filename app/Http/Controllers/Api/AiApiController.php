<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;

class AiApiController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Run AI tool
     */
    public function run(Request $request): JsonResponse
    {
        $request->validate([
            'tool' => 'required|string|in:chat,summarize,title,blog,seo,translate,sentiment,rewrite,code,custom',
            'input' => 'required|string|max:10000',
            'language' => 'nullable|string|in:en,ar',
            'options' => 'nullable|array',
        ]);

        $tool = $request->input('tool');
        $input = $request->input('input');
        $language = $request->input('language', 'en');
        $options = $request->input('options', []);

        $result = match ($tool) {
            'chat' => $this->aiService->chat($input, config('ai.prompts.chat')),
            'summarize' => $this->aiService->summarize($input, $language),
            'title' => $this->aiService->generateTitleDescription($input, $language),
            'blog' => $this->aiService->generateBlog($input, $language, $options['word_count'] ?? 500),
            'seo' => $this->aiService->generateSeoKeywords($input, $language),
            'translate' => $this->aiService->translate($input, $options['target_language'] ?? 'ar'),
            'sentiment' => $this->aiService->analyzeSentiment($input),
            'rewrite' => $this->aiService->rewrite($input, $options['style'] ?? 'professional'),
            'code' => $this->aiService->explainCode($input, $language),
            'custom' => $this->aiService->runCustomPrompt($input, $options['system_prompt'] ?? null),
            default => ['success' => false, 'error' => 'Unknown tool'],
        };

        return response()->json($result);
    }

    /**
     * Chat endpoint for widget
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $result = $this->aiService->chat(
            $request->input('message'),
            config('ai.prompts.chat')
        );

        return response()->json($result);
    }

    /**
     * Summarize text
     */
    public function summarize(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:10000',
            'language' => 'nullable|string|in:en,ar',
        ]);

        $result = $this->aiService->summarize(
            $request->input('text'),
            $request->input('language', 'en')
        );

        return response()->json($result);
    }

    /**
     * Generate title and description
     */
    public function generateTitle(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'language' => 'nullable|string|in:en,ar',
        ]);

        $result = $this->aiService->generateTitleDescription(
            $request->input('content'),
            $request->input('language', 'en')
        );

        return response()->json($result);
    }

    /**
     * Generate blog post
     */
    public function generateBlog(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'language' => 'nullable|string|in:en,ar',
            'word_count' => 'nullable|integer|min:100|max:3000',
        ]);

        $result = $this->aiService->generateBlog(
            $request->input('topic'),
            $request->input('language', 'en'),
            $request->input('word_count', 500)
        );

        return response()->json($result);
    }

    /**
     * Translate text
     */
    public function translate(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'target_language' => 'required|string|in:en,ar',
        ]);

        $result = $this->aiService->translate(
            $request->input('text'),
            $request->input('target_language')
        );

        return response()->json($result);
    }

    /**
     * Analyze sentiment
     */
    public function sentiment(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000',
        ]);

        $result = $this->aiService->analyzeSentiment($request->input('text'));

        return response()->json($result);
    }
}
