<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserListTest extends TestCase
{
    protected $accessToken;
    protected $accessToken2;
    protected $testUser;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->accessToken = $this->getToken();
        $this->accessToken2 = $this->getTokenNonAdmin();
    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    public function test_users_listing_authenticated()
    {
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('users'),['per_page' => 1]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
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
            ]
        ]);
    }

    public function test_users_listing_unauthenticated()
    {
        $response = $this->withToken($this->accessToken2)
                    ->get(route('users'), ['per_page' => 10]);
        $response->assertStatus(403);
    }

    public function test_user_status_pending()
    {
        $response = $this->withToken($this->accessToken)
                    ->get(route('users'),["status" => false,"per_page" => 1]);
        $response->assertStatus(200);
    }

    public function test_user_status_approved()
    {
        $response = $this->withToken($this->accessToken)
                    ->get(route('users'),["status" => true,"per_page" => 1]);
        $response->assertStatus(200);
    }    
}
