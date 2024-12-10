<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use App\Models\User;
use Tests\TestCase;

class UserSingleTest extends TestCase
{
    private $faker;
    protected $accessToken;
    protected $testLoginUser;

    function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('en_US');
        $this->accessToken = $this->getToken();
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    // Test valid data
    public function test_valid_data()
    {
        $id = User::inRandomOrder()->first()->value('id');
        $res = $this->getUser($id);
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'message',
            'data' => [
                    'user_id',
                    'code',
                    'name',
                    'telephone_1',
                    'email',
                    'dob',
                    'gender',
                    'final_confirmation',
                    'company',
                    'submitted_date',
                    'deposit_date',
                    'start_date',
                    'end_date',
                    'status',
                    'role',
                    'referral_user',
                    'memberApplication',
                    'registration_date',
                    'bankbook',
                    'idCard',
                    'sns',
                    'qr_code_url'
                
            ]
        ]);        
    }

    // Test invalid email test
    public function test_invalid_user_id()
    {
        $id = rand(20000,40000);
        $res = $this->withToken($this->accessToken,'Bearer')
                 ->get(route('user.single',['user_id' => $id]));
        $res->assertStatus(422);
    }


    // comman method for testing
    public function getUser($id)
    {
        $res = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('user.single', $id));
        return $res;
    }
}
