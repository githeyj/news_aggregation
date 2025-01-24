<?php

namespace App\Library;

use App\Contracts\NewsScrapperContract;
use App\DTO\ArticleDTO;
use Exception;
use Illuminate\Support\Collection;

class NewsScrapper
{
    public function __construct(
        private readonly NewsScrapperContract $scrapper
    ) {}

    /**
     * Run the scraper and return collected articles
     *
     * @return Collection<ArticleDTO>
     */
    public function run(int $page = 0): Collection
    {
        if (!$this->scrapper->isEnabled()) {
            throw new Exception("Scraper {$this->getScrapperName()} is not enabled");
        }

        if (!$this->scrapper->healthCheck()) {
            throw new Exception("Scraper {$this->getScrapperName()} is not healthy");
        }

        return collect($this->scrapper->fetch($page));
    }

    /**
     * Get the name of the current scraper
     */
    public function getScrapperName(): string
    {
        return $this->scrapper->getSourceName();
    }

    /**
     * Get the source URL of the current scraper
     */
    public function getScrapperUrl(): string
    {
        return $this->scrapper->getSourceUrl();
    }

    /**
     * Check if the current scraper is healthy
     */
    public function isHealthy(): bool
    {
        return $this->scrapper->healthCheck();
    }

    /**
     * Get the article limit for the current scraper
     */
    public function getLimit(): int
    {
        return $this->scrapper->getArticleLimit();
    }

    /**
     * Check if the current scraper is enabled
     */
    public function isEnabled(): bool
    {
        return $this->scrapper->isEnabled();
    }
}
