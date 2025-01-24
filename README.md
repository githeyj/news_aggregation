# News Aggregator
This is a news aggregator platform based on the amazing Laravel PHP framework.

## Getting Started
- Clone the repository
- Copy `env.example` to `.env` located on the `root` folder. 
- Create a database (`postgre`,`mysql`,`sqlite`). Update the `.env` file accordingly.
- Get PHP packages via Composer

```bash
composer install
```

- Run the migrations

```bash
php artisan migrate
```

- Run the seeder

```bash
php artisan db:seed
```

- Fetch API keys form the following news sources
  - [NewsAPI](https://newsapi.org/docs/get-started)
  - [NYTimes](https://developer.nytimes.com/docs/articlesearch-product/1/overview)
  - [TheGuardian](https://open-platform.theguardian.com/documentation)

#### Testing out the News Scrapping
Run the following command in your terminal:

```bash
php artisan app:scrap-news
```

The scrapping will then be processed in a job `App\Jobs\StartNewsScrapping`.
If you have configured the `QUEUE_CONNECTION` to a value other than `sync`, 
you might have to run `php artisan queue:work` to process the queue.

## Auto-updating the news from the sources
A command has been scheduled to automatically run twice on a daily basis and update the news articles.
It can be located under `./routes/console.php`.

```php
Schedule::command(ScrapNews::class)
    ->twiceDaily()
    ->withoutOverlapping();
```

Ensure to add the following command to `crontab`

```bash
# crontab -e
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Adding more scrappers
All scrappers must **implement** `App\Contracts\NewsScrapperContract`. 
- Create a new scrapper service file under `app/Services/MyScrapperService`
- Provide implementations to all methods required
- Add the entries to your new service under `config/services` in this format:

```php
// config/services.php
return [
  // ...
  'myscrapper' => [
    'enabled' => env('MYSCRAPPER_ENABLED', false),
    'key' => env('MYSCRAPPER_KEY'),
  ],
];
```

- Update your `.env` file accordingly

That is all that is required: **Simple & Direct**.


## Project Overview
### Requirements:
1. Data aggregation and storage: Implement a backend system that fetches articles from selected data sources
(choose at least 3 from the provided list) and stores them locally in a database. Ensure that the data is regularly
updated from the live data sources.

2. API endpoints: Create API endpoints for the frontend application to interact with the backend. These endpoints
should allow the frontend to retrieve articles based on search queries, filtering criteria (date, category, source), and
user preferences (selected sources, categories, authors).
