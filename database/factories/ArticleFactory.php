<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'slug'        => Str::slug($title) . '-' . Str::random(6),
            'title'       => $title,
            'body'        => fake()->paragraphs(6, true),
            'thumbnail'   => fake()->imageUrl(800, 600, 'news', true),
            'is_deleted'  => false,
            'rating'      => fake()->randomFloat(1, 0, 5),  // 0.0 → 5.0
            'org_id'      => fake()->optional()->randomElement([null, 1, 2, 3]),
            'user_id'     => fake()->optional()->randomElement([null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
            'date_up'     => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
