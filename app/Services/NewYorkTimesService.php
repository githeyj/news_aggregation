<?php

namespace App\Services;

use App\Contracts\NewsScrapperContract;
use App\DTO\ArticleDTO;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewYorkTimesService implements NewsScrapperContract
{
    protected string $baseUrl = 'https://api.nytimes.com/svc/search/v2';
    protected ?string $apiKey;

    private int $articleLimit = 50;

    public function __construct()
    {
        $this->apiKey = config('services.nytimes.key');
    }

    public function isEnabled(): bool
    {
        return config('services.nytimes.enabled', false);
    }

    public function getArticleLimit(): int
    {
        return $this->articleLimit;
    }

    public function getSourceName(): string
    {
        return 'The New York Times';
    }

    public function getSourceUrl(): string
    {
        return 'https://www.nytimes.com';
    }

    public function healthCheck(): bool
    {
        try {
            $response = Http::get("{$this->baseUrl}/articlesearch.json", [
                'api-key' => $this->apiKey,
                'page' => 0,
                'fl' => 'web_url',
            ]);
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    public function fetch(int $page = 0): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/articlesearch.json", [
                'api-key' => $this->apiKey,
                'page' => $page,
                'sort' => 'newest',
                'begin_date' => Carbon::now()->subDay()->format('Ymd'),
                'fl' => 'web_url,uri,snippet,headline,pub_date,section_name,byline,source,multimedia,lead_paragraph',
            ]);

            if (!$response->successful()) {
                Log::error('New York Times API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $articles = $response->json()['response']['docs'] ?? [];

            return array_map([$this, 'transformArticle'], $articles);
        } catch (Exception $e) {
            Log::error('Error fetching NYTimes articles: ' . $e->getMessage());
            return [];
        }
    }

    public function transformArticle(array $rawArticle): ArticleDTO
    {
        $image = data_get($rawArticle, 'multimedia')[0] ?? null;
        if ($image) {
            $image = "https://www.nytimes.com/{$image['url']}";
        }

        $author = data_get($rawArticle, 'byline.original') ?? 'Unknown';
        if (Str::startsWith($author, 'By ')) {
            $author = Str::after($author, 'By ');
        }

        return new ArticleDTO(
            title: data_get($rawArticle, 'headline.main') ?? '',
            excerpt: data_get($rawArticle, 'snippet') ?? '',
            content: data_get($rawArticle, 'lead_paragraph') ?? '',
            service: $this->getSourceName(),
            source: data_get($rawArticle, 'source') ?? $this->getSourceName(),
            source_url: data_get($rawArticle, 'web_url') ?? $this->getSourceUrl(),
            image_url: $image,
            published_at: data_get($rawArticle, 'pub_date') ?? null,
            author: $author,
            category: data_get($rawArticle, 'section_name') ?? 'General',
        );
    }
}
