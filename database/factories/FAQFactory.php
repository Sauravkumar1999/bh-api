<?php

namespace Database\Factories;

use App\Models\FAQ;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FAQFactory extends Factory
{
    protected $model = FAQ::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(),
            'status' => 1,
        ];
    }
}
