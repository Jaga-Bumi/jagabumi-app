<?php

namespace Database\Factories;

use App\Models\Prize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prize>
 */
class PrizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Prize::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->words(3, true),
            'type'        => fake()->randomElement(['CERTIFICATE', 'COUPON']),
            'image_url'   => fake()->imageUrl(600, 600, 'prize', true),
            'description' => fake()->paragraph(3),
            'quest_id'    => fake()->unique()->randomElement([1, 2, 3, 4, 5, 6]),
        ];
    }
}
