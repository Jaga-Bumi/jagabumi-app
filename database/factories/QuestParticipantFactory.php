<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestParticipant>
 */
class QuestParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = QuestParticipant::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['REGISTERED', 'COMPLETED', 'APPROVED', 'REJECTED']);

        return [
            'joined_at' => now()->subDays(rand(1, 30)),
            'status' => $status,
            'video_url' => $status !== 'REGISTERED' 
                ? $this->faker->url() 
                : null,
            'description' => $status !== 'REGISTERED'
                ? $this->faker->paragraph()
                : null,
            'submission_date' => $status !== 'REGISTERED'
                ? now()->subDays(rand(1, 10))
                : null,

            'quest_id' => fake()->randomElement(Quest::pluck('id')),
            'user_id' => fake()->randomElement(User::pluck('id')),
        ];
    }
}
