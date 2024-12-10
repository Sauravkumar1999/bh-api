<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use App\Models\User;

class UserLoginTest extends TestCase
{
    protected $testUser;
    protected $fakerData;
    protected $form_data;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $faker = Faker::create();
        $this->testUser = User::create([
            'first_name' => $faker->firstName . time() . 'u',
            'last_name' => $faker->lastName,
            'code' => $faker->regexify('[A-Za-z0-9]{20}'),
            'email' => $faker->unique()->safeEmail . time() . 'u',
            'password' => bcrypt('password123'),
        ]);

        $this->form_data = [
            'email'     => env('TEST_LOGIN_EMAIL'),
            'password'  => env('TEST_LOGIN_PASSWORD')
        ];
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    public function test_login()
    {
        // Setup valid user login
        $form_data = $this->form_data;
        $response = $this->login($form_data);

        if ($response->assertStatus(200)) {
            // save response data for validation
            $data = $response->original;

            // Ensure that login response has access_token variable
            $this->assertArrayHasKey('access_token', $data['data']);

            // Ensure that the data type returned is an object
            $this->assertTrue(gettype($data) == "array");

            // Store token to a new variable
            $token = $data['data']['access_token'];

            if ($response->assertStatus(200)) {
                // Ensure valid user login
                $user =    $this->withToken($token, 'Bearer')
                    ->get(route('me'));

                $user->assertSuccessful();
            };
        }
    }
    public function test_invalid_credentials()
    {
        // Setup invalid login credentials
        $form_data = [
            'email'         => $this->testUser->email,
            'password'      => $this->testUser->password
        ];

        // login with invalid credentials
        $response = $this->login($form_data);
        $response->assertStatus(404);
    }

    public function login($form_data)
    {
        return $this->post(route('login'), $form_data);
    }
}
