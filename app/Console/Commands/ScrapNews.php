<?php

namespace App\Console\Commands;

use App\Jobs\StartNewsScrapping;
use Illuminate\Console\Command;

class ScrapNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrap-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape news from configured sources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting news scraping...');

        // move the scrapping to a job
        StartNewsScrapping::dispatch();

        $this->info('News scraping shall start in a few seconds.');

        return self::SUCCESS;
    }
}
