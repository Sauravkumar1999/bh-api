<?php

namespace Database\Factories;

use App\Models\Allowance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AllowanceFactory extends Factory
{
    protected $model = Allowance::class;

    public function definition()
    {
        return [
            'payment_month' => date('m'),
            'member_id' => User::factory(),
            'referral_bonus' => $this->faker->randomFloat(2, 100, 1000),
            'commission' => $this->faker->randomFloat(2, 100, 1000),
            'headquarters_representative_allowance' => rand(1, 100),
            'organization_division_allowance' => $this->faker->randomFloat(2, 100, 1000),
            'other_allowances' => $this->faker->randomFloat(2, 100, 1000),
            'income_tax' => $this->faker->randomFloat(2, 100, 1000),
            'resident_tax' => $this->faker->randomFloat(2, 100, 1000),
            'year_end_settlement' => $this->faker->randomFloat(2, 100, 1000),
            'other_deductions_1' => $this->faker->randomFloat(2, 100, 1000),
            'other_deductions_2' => $this->faker->randomFloat(2, 100, 1000),
            'total_deduction' => $this->faker->randomFloat(2, 100, 1000),
            'total_before_tax' => $this->faker->randomFloat(2, 100, 1000),
            'policy_allowance' => $this->faker->randomFloat(2, 100, 1000),
            'deducted_amount_received' => $this->faker->randomFloat(2, 100, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
