<?php

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Complaint>
     */
    protected $model = Complaint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(3),
            'category' => fake()->randomElement([
                'wegen',
                'openbare_verlichting',
                'afval',
                'groen',
                'overlast',
                'openbare_ruimte',
                'water',
                'overig',
            ]),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['open', 'in_behandeling', 'opgelost']),
            'location' => fake()->streetAddress(),
            'lat' => fake()->latitude(51.5, 53.5),
            'lng' => fake()->longitude(3.0, 7.5),
            'reporter_name' => fake()->name(),
            'reporter_email' => fake()->unique()->safeEmail(),
            'reporter_phone' => fake()->phoneNumber(),
            'internal_notes' => null,
            'resolved_at' => null,
            'assigned_to' => null,
        ];
    }
}

