<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserFactory extends Factory
{

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $this->faker = Faker::create('en_US');
        $referralCode = Str::random(6);
        // $password = $this->faker->regexify('[A-Za-z0-9]{10}');
        $data = [
            'code'               => $this->getUniqueCode(),
            'user_type'          => $this->faker->randomElement(['agency', 'member']),
            'first_name'         => $this->faker->firstName,
            'last_name'          => $this->faker->lastName,
            'username'           => $this->faker->userName,
            'email'              => $this->faker->unique()->safeEmail,
            'dob'                => $this->faker->date(),
            'gender'             => $this->faker->randomElement(['male', 'female']),
            'bank_account_no'    => $this->faker->bankAccountNumber,
            'email_verified_at'  => now(),
            'password'           => bcrypt('Password@123'),
            'status'             => 1,
            'member_status'      => $this->faker->randomElement(['Active', 'Inactive']),
            'last_login'         => $this->faker->dateTimeThisMonth(),
            'remember_token'     => Str::random(10),
            'final_confirmation' => $this->faker->dateTimeBetween('now', 'now'),
            'submitted_date'     => $this->faker->dateTimeBetween('-1 year', 'now'),
            'deposit_date'       => $this->faker->dateTimeBetween('-6 months', 'now'),
            'start_date'         => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date'           => $this->faker->dateTimeBetween('now', '+2 years'),
            'company_id'         => $this->faker->numberBetween(1, 10),
            'bank_id'            => $this->faker->numberBetween(1, 5),
            'created_at'         => now()->subMonths(2),
            'updated_at'         => now()->subMonths(1),
            '_lft'               => $this->faker->numberBetween(1, 100),
            '_rgt'               => $this->faker->numberBetween(101, 200),
            'parent_id'          => null,
            'deleted_at'         => null,
            'subscription_type'  => $this->faker->randomElement(['free', 'paid']),
        ];
        return $data;
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $user->plain_password = 'Password@123';
        });
    }

    protected function getUniqueCode()
    {
        do {
            $code = rand(100000000, 999999999);
            $exists = User::where('code', $code)->exists();
        } while ($exists);

        return $code;
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
