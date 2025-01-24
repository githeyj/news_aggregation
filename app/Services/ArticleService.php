<?php

namespace App\Services;

use App\DTO\ArticleDTO;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * Save or update an article from DTO
     */
    public function saveFromDTO(ArticleDTO $articleDTO): Article
    {
        return DB::transaction(function () use ($articleDTO) {
            $article = Article::updateOrCreate(
                ['slug' => $articleDTO->slug],
                $articleDTO->toArray()
            );

            // Attach category if exists
            if ($articleDTO->category) {
                $category = Category::firstOrCreate([
                    'slug' => Str::slug($articleDTO->category),
                ], ['name' => $articleDTO->category]);

                if ($category) {
                    $article->categories()->sync([$category->id]);
                }
            }

            return $article;
        });
    }

    /**
     * Save multiple articles from DTOs
     *
     * @param array<ArticleDTO> $articleDTOs
     * @return array<Article>
     */
    public function saveMany(array $articleDTOs): array
    {
        return array_map(
            fn(ArticleDTO $dto) => $this->saveFromDTO($dto),
            $articleDTOs
        );
    }
}
