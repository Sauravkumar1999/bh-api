<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    protected static $accessToken;
    protected static $adminToken;

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
    {   // Ensure tokens and users are set up only once
        if (!isset(self::$accessToken)) {
            self::$accessToken = $this->getTokenNonAdmin(); 
        }
        if (!isset(self::$adminToken)) {
            self::$adminToken = $this->getToken();
        }
    }

    /**
     * Test successful delete.
     *
     * @return void
     */
    public function test_successful_delete()
    {
        $response = $this->delete_user(self::$accessToken);
        $response->assertStatus(200);
    }

    /**
     * Test unsuccessful delete.
     *
     * @return void
     */
    public function test_unsuccessful_delete()
    {
        $response = $this->delete_user(self::$adminToken);
        $response->assertStatus(401);
    }


    
    public function delete_user($accessToken)
    {
        return $this->withToken($accessToken)->delete(route('user.delete'));
    }
}
