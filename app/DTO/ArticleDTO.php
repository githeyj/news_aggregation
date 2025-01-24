<?php

namespace App\DTO;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ArticleDTO
{
    public readonly string $slug;

    public function __construct(
        public readonly string $title,
        public readonly string $excerpt,
        public readonly string $content,
        public readonly string $service,
        public readonly string $source,
        public readonly string $source_url,
        public readonly ?string $image_url = null,
        public readonly ?string $published_at = null,
        public readonly ?string $author = null,
        public readonly ?string $category = null,
    ) {
        $this->slug = Str::slug($title);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            excerpt: substr($data['excerpt'], 0, 300),
            content: $data['content'],
            service: $data['service'],
            source: $data['source'],
            source_url: $data['source_url'],
            image_url: $data['image_url'],
            published_at: $data['published_at'] ? Carbon::parse($data['published_at']) : null,
            author: $data['author'],
            category: $data['category'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => Str::limit($this->excerpt, 297),
            'content' => $this->content,
            'image_url' => $this->image_url,
            'published_at' => $this->published_at ? Carbon::parse($this->published_at)->toDateTimeString() : null,
            'author' => $this->author,
            'source' => $this->source,
            'service' => $this->service,
            'source_url' => $this->source_url,
            'category' => $this->category ?? Category::inRandomOrder()->first()?->name ?? null,
        ];
    }
}
