<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'code' => 'P0000'.Str::random(3),
            'product_name' => $this->faker->word,
            'product_description' => $this->faker->sentence,
            'product_price' => $this->faker->randomFloat(2, 10, 1000),
            'commission_type' => $this->faker->randomElement(['fixed-price', 'with-ratio']),
            'main_url' => $this->faker->url,
            'url_params' => json_encode($this->faker->words(3)),
            'url_1' => $this->faker->url,
            'url_2' => $this->faker->url,
            'urls_open_mode' => $this->faker->randomElement(['same-window']),
            'sale_rights_disclosure' => $this->faker->randomElement(['full', 'partial']),
            'approval_rights_disclosure' => $this->faker->randomElement(['full', 'partial']),
            'group' => $this->faker->word,
            'branch_representative' => $this->faker->name,
            'referral_bonus' => $this->faker->randomFloat(2, 0, 500),
            'other_fees' => $this->faker->randomFloat(2, 0, 100),
            'bh_operating_profit' => $this->faker->randomFloat(2, 0, 1000),
            'user_id' => \App\Models\User::factory(),
            'company_id' => \App\Models\ProductCompany::factory(),
            'exposer_order' => $this->faker->numberBetween(1, 10),
            'product_commissions' => json_encode($this->faker->words(3)),
            'bh_sale_commissions' => $this->faker->randomFloat(2, 0, 500),
            'sale_status' => 1,
            'contact_notifications' => $this->faker->boolean(),
        ];
    }
}
