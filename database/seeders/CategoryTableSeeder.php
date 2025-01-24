<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Technology',
            'Science',
            'Health',
            'Business',
            'Entertainment',
            'Sports',
            'Politics',
            'Education',
            'Environment',
            'Culture',
            'Travel',
            'Art',
            'Music',
            'Food',
            'Fashion',
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category);

            Category::firstOrCreate([
                'slug' => $slug,
            ], [
                'name' => $category,
            ]);
        }
    }
}
