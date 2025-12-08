<?php

namespace Database\Factories;

use App\Models\Prize;
use App\Models\PrizeUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrizeUser>
 */
class PrizeUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = PrizeUser::class;

    public function definition(): array
    {
        return [
            'nft_token_id' => $this->faker->uuid(),
            'tx_hash'      => $this->faker->sha256(),
            'token_uri'    => $this->faker->url(),

            'prize_id' => fake()->randomElement(Prize::pluck('id')),
            'user_id'  => fake()->randomElement(User::pluck('id')),
        ];
    }
}
