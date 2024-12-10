<?php

use App\Models\Bulletin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BulletinFactory extends Factory
{
    protected $model = Bulletin::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'distinguish' => $this->faker->word,
            'attachment' => $this->faker->url,
            'permission' => json_encode([1, 2, 3]),
            'content' => $this->faker->paragraph,
            'user_id' => User::factory(),
            'view_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}
