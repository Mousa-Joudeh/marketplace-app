<?php

namespace App\Services\Admin;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GenerateAdminInsights
{
    public function generate(array $report): array
    {
        $key = config('services.openai.key');
        abort_if(blank($key), 503, 'AI insights are not configured.');

        try {
            $response = Http::withToken($key)->acceptJson()->connectTimeout(5)->timeout(20)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'store' => false,
                    'max_output_tokens' => 1000,
                    'instructions' => 'Write concise marketplace admin insights in Arabic. Use only the supplied analytics. Cite the dates and counts behind each observation. Distinguish changes in page visits from changes in new bookings. Do not infer conversion, causation, revenue or unique visitors. State when the data is too limited and frame suggested actions as proposals, not completed work.',
                    'input' => json_encode($report, JSON_THROW_ON_ERROR),
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'admin_insights',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'summary' => ['type' => 'string'],
                                    'observations' => ['type' => 'array', 'items' => ['type' => 'string']],
                                    'recommended_actions' => ['type' => 'array', 'items' => ['type' => 'string']],
                                ],
                                'required' => ['summary', 'observations', 'recommended_actions'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ]);
        } catch (ConnectionException) {
            abort(502, 'The AI service could not be reached.');
        }

        abort_unless($response->successful() && $response->json('status') === 'completed', 502, 'The AI service did not return a completed response.');

        $message = collect($response->json('output', []))
            ->where('type', 'message')
            ->flatMap(fn (array $item) => $item['content'] ?? [])
            ->firstWhere('type', 'output_text');
        $text = data_get($message, 'text');
        $insights = is_string($text) ? json_decode($text, true) : null;

        abort_unless(
            is_string($insights['summary'] ?? null)
                && $this->isStringList($insights['observations'] ?? null)
                && $this->isStringList($insights['recommended_actions'] ?? null),
            502,
            'The AI service returned invalid insights.',
        );

        return [
            'summary' => $insights['summary'],
            'observations' => $insights['observations'],
            'recommended_actions' => $insights['recommended_actions'],
        ];
    }

    private function isStringList(mixed $value): bool
    {
        if (! is_array($value) || ! array_is_list($value)) {
            return false;
        }

        return array_filter($value, 'is_string') === $value;
    }
}
