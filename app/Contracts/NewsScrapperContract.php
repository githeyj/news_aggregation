<?php

namespace App\Contracts;

use App\DTO\ArticleDTO;

interface NewsScrapperContract
{
    /**
     * Check if the scraper service is enabled in configuration
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Fetch articles from the news source
     *
     * @return array Array of articles with standardized structure
     */
    public function fetch(int $page): array;

    /**
     * Get the name of the news source
     *
     * @return string
     */
    public function getSourceName(): string;

    /**
     * Get the base URL of the news source
     *
     * @return string
     */
    public function getSourceUrl(): string;

    /**
     * Check if the scraper is working correctly
     *
     * @return bool
     */
    public function healthCheck(): bool;

    /**
     * Get the maximum number of articles to fetch per request
     *
     * @return int
     */
    public function getArticleLimit(): int;

    /**
     * Transform raw article data into standardized format
     *
     * @param array $rawArticle
     * @return ArticleDTO
     */
    public function transformArticle(array $rawArticle): ArticleDTO;
}
