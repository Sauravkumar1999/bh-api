<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReferralTest extends TestCase
{
    protected $accessToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();
    }

    /*
    * Shared setup that runs only once before all tests.
    */
    protected function sharedSetUp(): void
    {
        $this->accessToken = $this->getToken();
    }


    /**
     * Test case to fetch referrals for an admin user.
     */
    public function test_fetch_referrals_for_admin_user()
    {
        // Make a GET request to fetch referrals using the access token
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('referrals'),['per_page' => 1]);
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'current_page',
                'data' => [[
                    'user_id',
                    'name',
                    'company',
                    'role',
                    'referral_user'
                ]  
                ]
        ]
        ]);
    }

    /**
     * Test case to fetch referrals for a non-admin user.
     */
    public function test_fetch_referrals_for_non_admin_user()
    {
        // Make a GET request to fetch referrals using the access token
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('referrals'));

        $response->assertStatus(200);
    }

    /**
     * Test case to fetch referrals with pagination.
     */
    public function test_fetch_referrals_with_pagination()
    {
        // Make a GET request to fetch referrals with pagination using the access token
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('referrals', ['page' => 2]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'pagination'
        ]);
    }

    /** 
     * Reviewer name: Samson Olawoyin
     * Date: July 2nd 2024
     * Description: The test developer needs to review this test 2-06-2024
     * Test case to view the referral tree without an ID for non-admin user.
     * This should result in a 404 error because only admin users can view without specifying an ID.
     
     *public function test_view_referral_tree_without_id()
     *{
     *    // Make a GET request to view the referral tree without an ID using the access token
     *    $response = $this->withToken($this->accessToken,'Bearer')
     *                ->get(route('referralsTree',['id' => 0]));
     *    // Assert that the response status is 500 when id parameter is not been sent
     *    $response->assertStatus(404);
     *}
     */

    /**
     * Test case to view a specific referral tree.
     */
    public function test_view_referral_tree_with_id()
    {
        $this->setAdminUser();
        $referralId = $this->getAdminUser()->id;

        // Make a GET request to view the referral tree using the access token
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('referralsTree', ['id' => $referralId]));

        $response->assertStatus(200);
    }
}
