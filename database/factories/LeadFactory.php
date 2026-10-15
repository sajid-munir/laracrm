<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_title' => fake()->sentence(3),
            'customer_name' => fake()->name(),
            'phone' => fake()->numerify('03#########'),
            'email' => fake()->safeEmail(),
            'message' => fake()->sentence(),
            'assigned_agent_id' => null,
            'status' => 'In Process',
            'is_new' => true,
        ];
    }
}
