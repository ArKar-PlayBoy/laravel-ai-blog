<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateContent(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|min:3|max:500',
            'system_prompt' => 'nullable|string|max:2000',
        ]);

        $topic = $this->sanitize($request->input('topic'));
        $systemPrompt = $request->filled('system_prompt') 
            ? $this->sanitize($request->input('system_prompt')) 
            : null;

        try {
            $content = $this->aiService->generateContent($topic, $systemPrompt);
            
            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Generate Content Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate content. Please try again.',
            ], 500);
        }
    }

    public function generateTitles(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|min:3|max:500',
            'count' => 'nullable|integer|min:1|max:10',
        ]);

        $topic = $this->sanitize($request->input('topic'));
        $count = $request->input('count', 5);

        try {
            $titles = $this->aiService->generateTitles($topic, $count);
            
            return response()->json([
                'success' => true,
                'titles' => $titles,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Generate Titles Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate titles. Please try again.',
            ], 500);
        }
    }

    public function generateOutline(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|min:3|max:500',
        ]);

        $topic = $this->sanitize($request->input('topic'));

        try {
            $outline = $this->aiService->generateOutline($topic);
            
            return response()->json([
                'success' => true,
                'outline' => $outline,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Generate Outline Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate outline. Please try again.',
            ], 500);
        }
    }

    public function improveWriting(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:10|max:10000',
        ]);

        $text = $request->input('text');

        try {
            $improved = $this->aiService->improveWriting($text);
            
            return response()->json([
                'success' => true,
                'improved' => $improved,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Improve Writing Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to improve writing. Please try again.',
            ], 500);
        }
    }

    public function generateSEO(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:50|max:50000',
        ]);

        $title = $this->sanitize($request->input('title'));
        $content = $request->input('content');

        try {
            $seo = $this->aiService->generateSEO($title, $content);
            
            return response()->json([
                'success' => true,
                'seo' => $seo,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Generate SEO Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate SEO. Please try again.',
            ], 500);
        }
    }

    public function moderateContent(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:10|max:10000',
        ]);

        $text = $request->input('text');

        try {
            $result = $this->aiService->moderateContent($text);
            
            return response()->json([
                'success' => true,
                'moderation' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Moderate Content Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to moderate content. Please try again.',
            ], 500);
        }
    }

    public function summarize(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:20|max:10000',
            'max_length' => 'nullable|integer|min:50|max:300',
        ]);

        $text = $request->input('text');
        $maxLength = $request->input('max_length', 150);

        try {
            $summary = $this->aiService->summarize($text, $maxLength);
            
            return response()->json([
                'success' => true,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Summarize Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to summarize content. Please try again.',
            ], 500);
        }
    }

    public function generateHashtags(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:20|max:10000',
            'count' => 'nullable|integer|min:5|max:20',
        ]);

        $text = $request->input('text');
        $count = $request->input('count', 10);

        try {
            $hashtags = $this->aiService->generateHashtags($text, $count);
            
            return response()->json([
                'success' => true,
                'hashtags' => $hashtags,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Generate Hashtags Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate hashtags. Please try again.',
            ], 500);
        }
    }

    public function changeTone(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:10|max:10000',
            'tone' => 'required|string|in:formal,casual,professional,friendly,academic',
        ]);

        $text = $request->input('text');
        $tone = $request->input('tone');

        try {
            $result = $this->aiService->changeTone($text, $tone);
            
            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Change Tone Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to change tone. Please try again.',
            ], 500);
        }
    }

    public function expandContent(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|min:10|max:5000',
        ]);

        $text = $request->input('text');

        try {
            $expanded = $this->aiService->expandContent($text);
            
            return response()->json([
                'success' => true,
                'expanded' => $expanded,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Expand Content Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to expand content. Please try again.',
            ], 500);
        }
    }

    public function status(Request $request): JsonResponse
    {
        $demoMode = config('ai.demo_mode', env('AI_DEMO_MODE', false));
        $model = config('ai.model', env('OLLAMA_MODEL', 'phi3'));
        
        return response()->json([
            'demo_mode' => $demoMode,
            'model' => $model,
            'ollama_running' => !$demoMode,
        ]);
    }

    private function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
