<?php
// database/factories/Modules/Sale/Entities/SaleFactory.php

namespace Database\Factories\Modules\Sale\Entities;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        // Define the attributes to be generated for Sale model
        return [
            'product_id' => \Modules\Product\Entities\Product::factory(),
            'seller_id' => \App\Models\User::factory(),
            'code' => $this->faker->word.$this->faker->randomDigit(),
            'product_sale_day' => '2024-04-03 12:00:00',
            'fee_type' => 'fixed-price',
            'product_price' => $this->faker->randomFloat(2, 10, 1000),
            'sales_price' => $this->faker->randomFloat(2, 10, 1000),
            'sales_type' => '0',
            'take' => $this->faker->randomFloat(2, 10, 1000),
            'number_of_sales' => $this->faker->numberBetween(1, 100),
            'sales_information' => $this->faker->sentence,
            'operating_income' => 0,
            'sales_status' => 'proceeding',
            'user_id' => \App\Models\User::factory(),
        ];
    }
}

