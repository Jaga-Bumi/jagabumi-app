<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'created_by'      => null,
            'name'            => Str::limit($name, 30, ''),
            'slug'            => Str::slug($name) . '-' . Str::random(6),
            'handle'          => fake()->unique()->bothify(str_repeat('#', 30)),
            'org_email'       => fake()->unique()->companyEmail(),
            'desc'            => fake()->paragraph(),
            'motto'           => fake()->catchPhrase(),
            'banner_img'      => fake()->imageUrl(1200, 400, 'business', true),
            'logo_img'        => fake()->imageUrl(300, 300, 'business', true),
            'website_url'     => fake()->optional()->url(),
            'instagram_url'   => fake()->optional()->url(),
            'x_url'           => fake()->optional()->url(),
            'facebook_url'    => fake()->optional()->url(),
            'status'          => fake()->randomElement(['INACTIVE', 'ACTIVE', 'HIATUS']),
        ];
    }
}
