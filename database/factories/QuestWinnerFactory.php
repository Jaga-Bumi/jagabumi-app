<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestWinner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class QuestWinnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = QuestWinner::class;

    public function definition(): array
    {
        $isDistributed = $this->faker->boolean(40); // 40% distributed, 60% pending

        return [
            'quest_id' => fake()->randomElement(Quest::pluck('id')),
            'user_id' => fake()->randomElement(QuestParticipant::pluck('id')),
            'reward_distributed' => $isDistributed,

            'tx_hash' => $isDistributed
                ? $this->faker->sha256()
                : null,

            'distributed_at' => $isDistributed
                ? now()->subDays(rand(1, 7))
                : null,
        ];
    }
}
