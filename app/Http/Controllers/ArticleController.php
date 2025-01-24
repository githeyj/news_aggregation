<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * Get paginated articles with optional filters
     */
    public function index(ArticleRequest $request)
    {
        $query = Article::query()
            ->with('categories')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            })
            ->when($request->source, function ($query, $source) {
                $query->where('source', $source);
            })
            ->when($request->author, function ($query, $author) {
                $query->where('author', $author);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->where('published_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->where('published_at', '<=', $date);
            })
            ->latest('published_at');

        // limits/pagination
        $limit = $request->per_page ?? 15;
        // protect against abuse
        if ($limit > 100) {
            $limit = 100;
        }

        return ArticleResource::collection($query->paginate($limit));
    }

    /**
     * Get a specific article by slug
     */
    public function show(string $slug)
    {
        $article = Article::with('categories')
            ->where('slug', $slug)
            ->firstOrFail();

        return new ArticleResource($article);
    }
}
