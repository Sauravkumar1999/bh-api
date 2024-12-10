<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use  App\Services\SessionService;

class VerifyPhoneTest extends TestCase
{
    private $faker;
    private $form_data;
    private $existing_user;
    protected $contacts;
    protected $otp;
    protected $sessionService;

    function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create('en_US');
        $this->existing_user = \App\Models\User::inRandomOrder()->first();
        $this->contacts = \App\Models\Contact::where('user_id', $this->existing_user->id)->first();
        $this->sessionService = new SessionService();
        $this->form_data = [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify(str_repeat('#', 10)),
            'type'  => 'find-password',
        ];
        $this->otp = strval(mt_rand(100000, 999999));

        $this->sessionService->createSession([
            'user_id' => $this->existing_user->id,
            'key' => 'otp',
            'value' => strval($this->otp),
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);        
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    //test with valid data
    public function test_valid_data()
    {
        $data = $this->form_data;
        $data['name'] = $this->existing_user->first_name . ' ' . $this->existing_user->last_name;
        $data['id'] = $this->existing_user->email;
        $data['phone'] = $this->contacts->telephone_1;
        $data['otp']   = $this->otp;
        $response = $this->post(route('auth.verify_user'), $data);
        $response->assertStatus(200);
    }
    //test with valid data type find-id
    public function test_valid_data_find_id()
    {
        $data = $this->form_data;
        $data['name']  = $this->existing_user->first_name . ' ' . $this->existing_user->last_name;
        $data['phone'] = $this->contacts->telephone_1;
        $data['type']  = 'find-id';
        $data['otp']   = $this->otp;
        $response = $this->post(route('auth.verify_user'), $data);
        $response->assertStatus(200);
    }
    // test with invalid data
    public function test_invalid_data()
    {
        $data = $this->form_data;
        $response = $this->post(route('auth.verify_user'), $data);
        $response->assertStatus(422);
    }
    // Test with invalid email
    public function test_invalid_email()
    {
        $data = $this->form_data;
        $data['name'] = $this->existing_user ? $this->existing_user->first_name . ' ' . $this->existing_user->last_name : 'N/A';
        $data['phone'] = $this->existing_user && $this->existing_user->contacts->isNotEmpty() ? $this->contacts->first()->telephone_1 : '1010101010';
        $response = $this->post(route('auth.verify_user'), $data);
        $response->assertStatus(422);
    }

    // // Test with invalid name
    public function test_invalid_name()
    {
        $data = $this->form_data;
        $data['email'] = $this->existing_user ? $this->existing_user->email : 'N/A';
        $data['phone'] = $this->existing_user && $this->existing_user->contacts->isNotEmpty() ? $this->contacts->telephone_1 : '1010101010';
        $response = $this->post(route('auth.verify_user'), $data);
        $response->assertStatus(422);
    }

    // Test with invalid phone
    public function test_invalid_phone()
    {
        $data = $this->form_data;
        $data['name'] = $this->existing_user ? $this->existing_user->first_name . ' ' . $this->existing_user->last_name : 'N/A';
        $data['email'] = $this->existing_user ? $this->existing_user->email : 'N/A';
        $response = $this->post(route('auth.verify_user'), $data);
        $response->assertStatus(422);
    }
}
