<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Organization;
use App\Models\User;
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
            'org_id'      => fake()->optional()->randomElement(Organization::pluck('id')),
            'user_id'     => fake()->optional()->randomElement(User::pluck('id')),
        ];
    }
}
    