<?php

namespace Database\Factories;

use App\Models\AllowancePayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class AllowancePaymentFactory extends Factory
{
    protected $model = AllowancePayment::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'detail' => $this->faker->paragraph,
            'user_id' => User::factory(),
        ];
    }
}