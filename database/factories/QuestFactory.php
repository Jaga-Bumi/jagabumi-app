<?php

namespace Database\Factories;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quest>
 */
class QuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Quest::class;

    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();

        $registrationStart = fake()->dateTimeBetween('now', '+5 days');
        $registrationEnd   = fake()->dateTimeInInterval($registrationStart, '+5 days');

        $questStart        = fake()->dateTimeInInterval($registrationEnd, '+3 days');
        $questEnd          = fake()->dateTimeInInterval($questStart, '+4 days');

        $judgingStart      = fake()->dateTimeInInterval($questEnd, '+2 days');
        $judgingEnd        = fake()->dateTimeInInterval($judgingStart, '+3 days');

        $prizeDate         = fake()->dateTimeInInterval($judgingEnd, '+4 days');

        return [
            'title'                   => $title,
            'slug'                    => Str::slug($title) . '-' . Str::random(6),
            'desc'                    => fake()->paragraph(4),
            'banner_url'              => fake()->imageUrl(1200, 600, 'event', true),
            'location_name'           => fake()->city(),
            'latitude'                => fake()->randomFloat(8, -90, 90),
            'longitude'               => fake()->randomFloat(8, -180, 180),
            'radius_meter'            => 100,
            'liveness_code'           => fake()->optional()->bothify('LIVE-####'),
            'registration_start_at'   => $registrationStart,
            'registration_end_at'     => $registrationEnd,
            'quest_start_at'          => $questStart,
            'quest_end_at'            => $questEnd,
            'judging_start_at'        => $judgingStart,
            'judging_end_at'          => $judgingEnd,
            'prize_distribution_date' => $prizeDate,
            'status'                  => fake()->randomElement([
                'IN REVIEW', 'REJECTED', 'APPROVED', 'ACTIVE', 'ENDED', 'CANCELLED'
            ]),
            'participant_limit'       => fake()->numberBetween(10, 200),
            'winner_limit'            => fake()->numberBetween(1, 10),
            'approval_date'           => fake()->optional()->dateTimeBetween('now', '+5 days'),
            'org_id'                  => fake()->randomElement([1, 2, 3]),
        ];
    }
}
