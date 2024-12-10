<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    protected static $accessToken;
    protected static $refreshToken;


    protected function setUp(): void
    {
        parent::$wasSetup = true;
        parent::setUp();
    }

    /**
     * Test valid refresh token data.
     *
     * @return void
     */
    public function test_valid_refresh_token()
    {
        $this::$refreshToken = $this->getRefreshTokenAdmin();

        $response = $this->refreshAccessToken($this::$refreshToken);

        $response->assertStatus(200);

        $this->checkReturnedDataStructure($response);
    }

    /**
     * Test invalid refresh token data
     *
     * @return void
     */


    public function test_invalid_refresh_token()
    {
        $this::$refreshToken = $this->faker->uuid();

        $response = $this->refreshAccessToken($this::$refreshToken);

        $response->assertStatus(422);
    }

    /**
     * Check data structre for valid refresh token
     *
     * @return void
     */

    private function checkReturnedDataStructure($response)
    {
        $response->assertJsonStructure([
            'access_token',
            'expires_in',
            'cookie',
            'cookie',
            'refresh_token'
        ]);
    }

    /**
     * Send api to refresh the access token
     *
     * @return TestResponse
     */

    private function refreshAccessToken($refreshToken)
    {
        return $this->post(route('login.refresh', ['refresh_token' => $refreshToken]));
    }
}
