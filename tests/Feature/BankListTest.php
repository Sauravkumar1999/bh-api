<?php

namespace Tests\Feature;

use Tests\TestCase;

class BankListTest extends TestCase
{
    protected static $accessToken;
    protected static $testUser;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();
    }

    /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {
        // Use the test user created in the TestCase class
        self::$testUser = $this->getTestUser();

        // Get tokens for admin and normal user
        self::$accessToken = $this->getToken();
        
    }

    public function test_bank_listing_authenticated()
    {
        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->get(route('bank'), ['per_page' => 1]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' =>
            'success',
            'message',
            'data' => [[
                'bank_name',
                'display_name',
                'status'
            ]]
        ]);
    }
}
