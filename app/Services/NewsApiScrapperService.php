<?php

namespace App\Services;

use App\Contracts\NewsScrapperContract;
use App\DTO\ArticleDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiScrapperService implements NewsScrapperContract
{
    private string $apiKey;
    private string $baseUrl = 'https://newsapi.org/v2';
    private int $articleLimit = 100;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
    }

    public function isEnabled(): bool
    {
        return config('services.newsapi.enabled', false);
    }

    public function fetch(int $page = 0): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        try {
            $response = Http::get("{$this->baseUrl}/top-headlines", [
                'apiKey' => $this->apiKey,
                'language' => 'en',
                'pageSize' => $this->getArticleLimit(),
                'page' => $page,
            ]);

            if (!$response->successful()) {
                Log::error('NewsAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $articles = $response->json()['articles'] ?? [];

            return array_map([$this, 'transformArticle'], $articles);
        } catch (\Exception $e) {
            Log::error('NewsAPI scraping failed', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function getSourceName(): string
    {
        return 'NewsAPI';
    }

    public function getSourceUrl(): string
    {
        return 'https://newsapi.org';
    }

    public function healthCheck(): bool
    {
        try {
            $response = Http::get("{$this->baseUrl}/top-headlines", [
                'apiKey' => $this->apiKey,
                'pageSize' => 1,
                'language' => 'en',
                'country' => 'us',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getArticleLimit(): int
    {
        return $this->articleLimit;
    }

    public function transformArticle(array $rawArticle): ArticleDTO
    {
        return new ArticleDTO(
            title: $rawArticle['title'] ?? '',
            excerpt: $rawArticle['description'] ?? '',
            content: $rawArticle['content'] ?? '',
            service: $this->getSourceName(),
            source: data_get($rawArticle, 'source.name') ?? $this->getSourceName(),
            source_url: $rawArticle['url'] ?? $this->getSourceUrl(),
            image_url: $rawArticle['urlToImage'] ?? null,
            published_at: $rawArticle['publishedAt'] ?? null,
            author: $rawArticle['author'] ?? null,
        );
    }
}
