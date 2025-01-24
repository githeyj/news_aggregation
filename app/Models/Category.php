<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}
