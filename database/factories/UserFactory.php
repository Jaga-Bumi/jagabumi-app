<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'handle' => fake()->optional()->unique()->bothify(str_repeat('#', 30)),
            'email' => fake()->unique()->safeEmail(),
            'bio' => fake()->optional()->text,
            'phone' => fake()->optional()->phoneNumber,
            'verifier_id' => fake()->optional()->unique()->numberBetween(1,100),
            'wallet_address' => fake()->optional()->unique()->bothify(str_repeat('#', 42)),
            'avatar_url' => fake()->optional()->imageUrl,
            'role' => fake()->randomElement(['USER', 'ORG_MAKER']),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
