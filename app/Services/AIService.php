<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $host;
    private string $model;
    private int $timeout;
    private bool $demoMode;

    public function __construct()
    {
        $this->host = config('ai.ollama_host', env('OLLAMA_HOST', 'http://localhost:11434'));
        $this->model = config('ai.model', env('OLLAMA_MODEL', 'phi3'));
        $this->timeout = config('ai.timeout', 120);
        $this->demoMode = config('ai.demo_mode', env('AI_DEMO_MODE', false));
    }

    public function generateContent(string $topic, ?string $systemPrompt = null): string
    {
        if ($this->demoMode) {
            return $this->demoGenerateContent($topic);
        }

        $prompt = $systemPrompt ?? "Write a comprehensive, well-structured blog post about: {$topic}. 
Include an introduction, main points with subheadings, and a conclusion. 
Make it engaging and informative for readers.";

        return $this->callOllama($prompt);
    }

    public function generateTitles(string $topic, int $count = 5): array
    {
        if ($this->demoMode) {
            return $this->demoGenerateTitles($topic, $count);
        }

        $prompt = "Generate {$count} catchy, SEO-friendly titles for a blog post about: {$topic}.
Return ONLY a JSON array of strings, nothing else. Example format: [\"Title 1\", \"Title 2\", \"Title 3\"]";

        $response = $this->callOllama($prompt);
        
        return $this->parseJsonArray($response, $count);
    }

    public function generateOutline(string $topic): array
    {
        if ($this->demoMode) {
            return $this->demoGenerateOutline($topic);
        }

        $prompt = "Generate a detailed blog post outline for: {$topic}.
Return ONLY a JSON array with this structure:
[{\"title\":\"Section Title\",\"subsections\":[\"Subsection 1\",\"Subsection 2\"]}]
Include at least 5 main sections with 2-3 subsections each.";

        $response = $this->callOllama($prompt);
        
        return $this->parseJsonArray($response, 5);
    }

    public function improveWriting(string $text): string
    {
        if ($this->demoMode) {
            return $this->demoImproveWriting($text);
        }

        $prompt = "Improve the following text for grammar, clarity, and style while keeping the original meaning:
---
{$text}
---
Return ONLY the improved text, no explanations or comments.";

        return $this->callOllama($prompt);
    }

    public function generateSEO(string $title, string $content): array
    {
        if ($this->demoMode) {
            return $this->demoGenerateSEO($title);
        }

        $prompt = "Based on this title '{$title}' and content excerpt, generate:
1. A meta description (max 160 characters)
2. 5 relevant keywords

Return ONLY a JSON object with keys 'meta_description' and 'keywords' (as array).
Example: {\"meta_description\":\"...\",\"keywords\":[\"kw1\",\"kw2\"]}";

        $response = $this->callOllama($prompt);
        
        return $this->parseJsonObject($response, [
            'meta_description' => 'AI-generated meta description for ' . $title,
            'keywords' => ['ai', 'blog', 'writing', 'content', 'seo']
        ]);
    }

    public function moderateContent(string $text): array
    {
        if ($this->demoMode) {
            return $this->demoModerateContent($text);
        }

        $prompt = "Analyze this text for inappropriate content. Check for:
- Spam
- Harassment or hate speech
- Violence
- Adult content

Return ONLY a JSON object:
{\"is_safe\":true,\"flags\":[],\"reason\":\"\"}
If unsafe, set is_safe:false and add specific flags.";

        $response = $this->callOllama($prompt);
        
        return $this->parseJsonObject($response, [
            'is_safe' => true,
            'flags' => [],
            'reason' => 'Content appears safe'
        ]);
    }

    public function summarize(string $text, int $maxLength = 150): string
    {
        if ($this->demoMode) {
            return $this->demoSummarize($text);
        }

        $prompt = "Summarize the following text into a concise summary of approximately {$maxLength} words.
Keep the key points and main ideas. Return ONLY the summary, no introductions.

---
{$text}
---";

        return $this->callOllama($prompt);
    }

    public function generateHashtags(string $text, int $count = 10): array
    {
        if ($this->demoMode) {
            return $this->demoGenerateHashtags($text, $count);
        }

        $prompt = "Extract {$count} relevant, popular hashtags from this text.
Return ONLY a JSON array of strings, each starting with #.
Example: [\"#topic1\",\"#topic2\"]";

        $response = $this->callOllama($prompt);
        
        return $this->parseJsonArray($response, $count);
    }

    public function changeTone(string $text, string $tone = 'formal'): string
    {
        if ($this->demoMode) {
            return $this->demoChangeTone($text, $tone);
        }

        $validTones = ['formal', 'casual', 'professional', 'friendly', 'academic'];
        if (!in_array($tone, $validTones)) {
            $tone = 'formal';
        }

        $prompt = "Rewrite the following text in a {$tone} tone.
Keep the same meaning but adjust the style and vocabulary.
Return ONLY the rewritten text, no explanations.

---
{$text}
---";

        return $this->callOllama($prompt);
    }

    public function expandContent(string $text): string
    {
        if ($this->demoMode) {
            return $this->demoExpandContent($text);
        }

        $prompt = "Expand on the following text with more details, examples, and elaboration.
Make it more comprehensive while keeping the same topic.
Return ONLY the expanded content, no introductions.

---
{$text}
---";

        return $this->callOllama($prompt);
    }

    private function callOllama(string $prompt): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->host}/api/generate", [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['response'] ?? 'No response generated';
            }

            Log::error('Ollama API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return "Error: Unable to generate content. Please try again.";
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Ollama connection error', ['message' => $e->getMessage()]);
            return "Error: Cannot connect to Ollama. Please ensure Ollama is running.";
        } catch (\Exception $e) {
            Log::error('AI Service error', ['message' => $e->getMessage()]);
            return "Error: " . $e->getMessage();
        }
    }

    private function parseJsonArray(string $response, int $defaultCount): array
    {
        preg_match('/\[[\s\S]*\]/', $response, $matches);
        
        if (!empty($matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return array_slice($decoded, 0, $defaultCount);
            }
        }

        return array_fill(0, $defaultCount, "Generated title " . rand(1, 100));
    }

    private function parseJsonObject(string $response, array $default): array
    {
        preg_match('/\{[\s\S]*\}/', $response, $matches);
        
        if (!empty($matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return array_merge($default, $decoded);
            }
        }

        return $default;
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    // Demo Mode Methods
    private function demoGenerateContent(string $topic): string
    {
        return "## Introduction\n\nWelcome to this comprehensive guide about **{$topic}**. In this article, we will explore the key aspects and provide valuable insights.\n\n## Key Points\n\n### Understanding the Basics\n\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\n\n### Main Discussion\n\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n\n## Conclusion\n\nIn conclusion, {$topic} is an important topic that deserves attention. We hope this guide provides you with valuable information.\n\n---\n*This is a demo response. Enable Ollama and set AI_DEMO_MODE=false in .env for real AI-generated content.*";
    }

    private function demoGenerateTitles(string $topic, int $count): array
    {
        $templates = [
            "The Ultimate Guide to {$topic}",
            "{$topic}: Everything You Need to Know",
            "10 Essential Tips for {$topic}",
            "How {$topic} Can Transform Your Approach",
            "Mastering {$topic}: A Beginner's Guide",
            "Why {$topic} Matters More Than Ever",
            "{$topic} Best Practices for 2024",
            "The Future of {$topic}: Trends to Watch",
        ];
        
        return array_slice($templates, 0, $count);
    }

    private function demoGenerateOutline(string $topic): array
    {
        return [
            ['title' => 'Introduction', 'subsections' => ['Background', 'Importance', 'What You\'ll Learn']],
            ['title' => 'Getting Started', 'subsections' => ['Prerequisites', 'Basic Concepts', 'Setting Up']],
            ['title' => 'Core Concepts', 'subsections' => ['Key Principles', 'Best Practices', 'Common Mistakes']],
            ['title' => 'Advanced Techniques', 'subsections' => ['Expert Strategies', 'Optimization Tips', 'Performance']],
            ['title' => 'Conclusion', 'subsections' => ['Summary', 'Next Steps', 'Resources']],
        ];
    }

    private function demoImproveWriting(string $text): string
    {
        return "**Improved Version:\n\n" . $text . "\n\n---\n*This is a demo response. Enable Ollama for real AI-powered improvements.*";
    }

    private function demoGenerateSEO(string $title): array
    {
        return [
            'meta_description' => "Learn everything about {$title}. Comprehensive guide with tips, tricks, and best practices for beginners and experts.",
            'keywords' => [$title, 'guide', 'tutorial', 'tips', 'best practices'],
        ];
    }

    private function demoModerateContent(string $text): array
    {
        $lowerText = strtolower($text);
        $flags = [];
        
        if (str_contains($lowerText, 'spam')) {
            $flags[] = 'spam';
        }
        if (str_contains($lowerText, 'hate') || str_contains($lowerText, 'stupid')) {
            $flags[] = 'harassment';
        }
        
        return [
            'is_safe' => empty($flags),
            'flags' => $flags,
            'reason' => empty($flags) ? 'Content appears safe' : 'Content flagged for: ' . implode(', ', $flags),
        ];
    }

    private function demoSummarize(string $text): string
    {
        $words = str_word_count($text, 1);
        $summaryLength = min(50, count($words));
        $summaryWords = array_slice($words, 0, $summaryLength);
        
        return "**Summary:**\n\n" . implode(' ', $summaryWords) . "...\n\n---\n*This is a demo summary. Enable Ollama for real AI-powered summarization.*";
    }

    private function demoGenerateHashtags(string $text, int $count): array
    {
        $commonTags = [
            '#blog', '#writing', '#content', '#tips', '#guide', 
            '#tutorial', '#howto', '#learn', '#education', '#digital',
            '#marketing', '#business', '#technology', '#lifestyle', '#news'
        ];
        
        return array_slice($commonTags, 0, $count);
    }

    private function demoChangeTone(string $text, string $tone): string
    {
        $toneExamples = [
            'formal' => 'I am writing to inform you that...',
            'casual' => 'Hey! Just wanted to let you know...',
            'professional' => 'This correspondence is to notify you...',
            'friendly' => 'Hope you\'re doing great! Just wanted to share...',
            'academic' => 'This research paper examines the concept of...'
        ];
        
        $example = $toneExamples[$tone] ?? $toneExamples['formal'];
        
        return "**{$tone} tone:**\n\n{$example}\n\n[Your text would be rewritten in {$tone} tone]\n\n---\n*This is a demo. Enable Ollama for real AI-powered tone changes.*";
    }

    private function demoExpandContent(string $text): string
    {
        $expanded = "## Expanded Version\n\n**Original:**\n{$text}\n\n**Expanded Content:**\n\n{$text}\n\nFurthermore, this topic encompasses several important aspects that deserve detailed exploration. \n\n### Key Considerations\n\n- First and foremost, understanding the fundamentals is crucial...\n- Additionally, practical implementation requires careful consideration...\n- Moreover, the implications extend beyond initial assumptions...\n\n### Practical Applications\n\nIn real-world scenarios, this knowledge can be applied in numerous ways. For instance:\n\n1. Improving daily workflows\n2. Enhancing decision-making processes\n3. Fostering better communication\n\n### Conclusion\n\nAs we have explored, this subject offers significant value when properly understood and applied.\n\n---\n*This is a demo expansion. Enable Ollama for real AI-powered content expansion.*";
        
        return $expanded;
    }
}
