<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    private $faker;
    private $form_data;

    function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('en_US');
        $referralCode = \App\Models\User::inRandomOrder()->first();
        $password = $this->generatePassword();

        $this->form_data = [
            'first_name'             => $this->faker->firstName(),
            'email'                  => $this->faker->unique()->safeEmail(),
            'password'               => $password,
            'password_confirmation'  => $password,
            'phone'                  => $this->faker->numerify(str_repeat('#', 10)),
            'dob'                    => $this->faker->date(),
            'gender'                 => $this->faker->randomElement(['male', 'female']),
            'post_code'              => $this->faker->postcode(),
            'address'                => $this->faker->address(),
            'address_detail'         => $this->faker->secondaryAddress(),
            'account_number'         => "'".$this->faker->numerify(str_repeat('#', 12))."'", // Generates a 12-digit number
            'bank_id'                => null, // Adjust if you have bank data
            'referral_code'          => $referralCode->code,
            'referral_code_verified' => $referralCode->code,
            'id_photo'               => null,
            'bankbook_photo'         => null
        ];
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    // Test valid data
    public function test_valid_data()
    {
        $data = $this->form_data;
        $response = $this->regsiter($data);
        $response->assertStatus(200);
    }

    // Test invalid email test
    public function test_invalid_email()
    {
        $data = $this->form_data;
        $data['email'] =  Str::random(10);
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }
    // Test existing email test
    public function test_existing_email()
    {
        $data = $this->form_data;
        $data['email'] =  $this->faker->randomElement(\App\Models\User::inRandomOrder()->limit(5)->pluck('email')->toArray());
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }
    // Test invalid password
    public function test_invalid_password()
    {
        $data = $this->form_data;
        $data['password'] = Str::random(6);
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }
    // Test invalid phone
    public function test_invalid_phone()
    {
        $data = $this->form_data;
        $data['phone'] = $this->faker->numerify(str_repeat('#', 8));
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }
    // Test existing phone
    public function test_existing_phone()
    {
        $data = $this->form_data;
        $data['phone'] = $this->faker->randomElement(\App\Models\Contact::inRandomOrder()->limit(5)->pluck('telephone_1')->toArray());
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }

    // test null first_name
    public function test_first_name()
    {
        $data = $this->form_data;
        $data['first_name'] = null;
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }

    // test null post_code
    public function test_post_code()
    {
        $data = $this->form_data;
        $data['post_code'] = null;
        $response = $this->post(route('register'), $data);
        $response->assertStatus(200);
    }

    // test invalid account_number
    public function test_account_number()
    {
        $data = $this->form_data;
        $data['account_number'] = $this->faker->numberBetween(10000, 99999);
        $response = $this->post(route('register'), $data);
        $response->assertStatus(422);
    }

    // comman method for testing
    public function regsiter($form_data)
    {
        return $this->post(route('register'), $form_data);
    }
    function generatePassword($minLength = 8, $maxLength = 20)
    {
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specials = '!@#$%^&*()_+';

        // Ensure each category is included at least once
        $password = $letters[rand(0, strlen($letters) - 1)]
            . $numbers[rand(0, strlen($numbers) - 1)]
            . $specials[rand(0, strlen($specials) - 1)];

        // Fill the remaining length with random characters from all categories
        $allCharacters = $letters . $numbers . $specials;
        $remainingLength = rand($minLength, $maxLength) - 3;

        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
        }

        // Shuffle the password to mix the characters
        return str_shuffle($password);
    }
}
