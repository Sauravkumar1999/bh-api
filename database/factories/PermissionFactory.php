<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'display_name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(6),
            'ltpm' => $this->faker->randomElement(['Test']),
        ];
    }
}
