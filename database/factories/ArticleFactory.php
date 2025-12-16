<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title . '-' . $this->faker->unique()->randomNumber()),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(3, true),
            'featured_image' => null,
            'author_id' => User::factory(),
            'category_id' => ArticleCategory::factory(),
            'is_published' => true,
            'is_featured' => $this->faker->boolean(20),
            'views_count' => $this->faker->numberBetween(0, 500),
            'tags' => [$this->faker->word(), $this->faker->word()],
            'meta_data' => ['title' => $title],
            'published_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}
