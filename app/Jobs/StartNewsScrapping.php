<?php

namespace App\Jobs;

use App\Library\NewsScrapper;
use App\Services\ArticleService;
use App\Services\GuardianService;
use App\Services\NewsApiScrapperService;
use App\Services\NewYorkTimesService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartNewsScrapping implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleService $articleService): void
    {
        $scrappers = [
            new NewsScrapper(new NewsApiScrapperService()),
            new NewsScrapper(new NewYorkTimesService()),
            new NewsScrapper(new GuardianService()),
        ];

        foreach ($scrappers as $scrapper) {
            try {
                if (!$scrapper->isEnabled()) {
                    Log::info("Scraper {$scrapper->getScrapperName()} is disabled. Skipping...");
                    continue;
                }

                Log::info("Scraping from {$scrapper->getScrapperName()}...");

                $articles = $scrapper->run(page: 1);

                Log::info("Found {$articles->count()} articles.");

                $savedArticles = $articleService->saveMany($articles->all());

                Log::info("Successfully saved " . count($savedArticles) . " articles");

            } catch (Exception $e) {
                Log::error('News scraping failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e; // Re-throw to mark job as failed
            }
        }
    }
}
