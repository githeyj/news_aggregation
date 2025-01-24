<?php

namespace App\Services;

use App\Contracts\NewsScrapperContract;
use App\DTO\ArticleDTO;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianService implements NewsScrapperContract
{
    protected string $baseUrl = 'https://content.guardianapis.com';
    protected ?string $apiKey;

    private int $articleLimit = 50;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
    }

    public function isEnabled(): bool
    {
        return config('services.guardian.enabled', false);
    }

    public function getArticleLimit(): int
    {
        return $this->articleLimit;
    }

    public function getSourceName(): string
    {
        return 'The Guardian';
    }

    public function getSourceUrl(): string
    {
        return 'https://www.theguardian.com';
    }

    public function healthCheck(): bool
    {
        try {
            $response = Http::get("{$this->baseUrl}/search", [
                'api-key' => $this->apiKey,
                'page-size' => 1,
            ]);
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    public function fetch(int $page = 1): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/search", [
                'api-key' => $this->apiKey,
                'page' => $page,
                'page-size' => $this->articleLimit,
                'order-by' => 'newest',
                'from-date' => Carbon::now()->subDay()->format('Y-m-d'),
            ]);

            if (!$response->successful()) {
                Log::error('Guardian API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $articles = $response->json()['response']['results'] ?? [];

            foreach ($articles as &$article) {
                $article = array_merge($article, $this->getSingleArticle($article['id']));
            }

            return array_map([$this, 'transformArticle'], $articles);
        } catch (Exception $e) {
            Log::error('Error fetching Guardian articles: ' . $e->getMessage());
            return [];
        }
    }

    public function transformArticle(array $rawArticle): ArticleDTO
    {
        return new ArticleDTO(
            title: data_get($rawArticle, 'webTitle', ''),
            excerpt: data_get($rawArticle, 'content'),
            content: data_get($rawArticle, 'content'),
            service: $this->getSourceName(),
            source: $this->getSourceName(),
            source_url: data_get($rawArticle, 'webUrl', $this->getSourceUrl()),
            image_url: $rawArticle['image'],
            published_at: data_get($rawArticle, 'webPublicationDate'),
            author: data_get($rawArticle, 'author'),
            category: data_get($rawArticle, 'sectionName', 'General'),
        );
    }

    private function getSingleArticle(string $id): array
    {
        $response = Http::get("{$this->baseUrl}/{$id}", query: [
            'api-key' => $this->apiKey,
            'show-fields' => 'bodyText,thumbnail,byline',
        ]);

        $content = $response->json()['response']['content'];

        return [
            'content' => data_get($content, 'fields.bodyText'),
            'image' => data_get($content, 'fields.thumbnail'),
            'author' => data_get($content, 'fields.byline'),
        ];
    }
}
