<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AllowancePayment;
use Tests\TestCase;
use App\Models\Role;

class AllowancePaymentTest extends TestCase
{
    protected static $accessToken;
    protected static $accessToken2;
    protected $normalUser;
    protected $formData;
    protected $allowance_payment;
    protected static $wasSetup = false;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        if (!static::$wasSetup) {
            static::$wasSetup = true;
            $this->sharedSetUp();
        }

        // Prepare form data
        $this->formData = [
            'title' => 'Sample Allowance Payment',
            'detail' => 'Details of the allowance payment',
            'user_id' => self::$adminUser->id,
        ];
    }

    /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {
        // Use the test user created in the TestCase class
        $this->normalUser = $this->getTestUser();
        $this->adminUser = $this->getAdminUser();

        // Get tokens for admin and normal user
        self::$accessToken = $this->getToken();
        self::$accessToken2 = $this->getTokenNonAdmin();
    }


    /**
     * Ensure tokens are populated.
     */
    protected function ensureTokensPopulated()
    {
        if (empty(self::$accessToken)) {
            self::$accessToken = $this->getToken();
        }

        if (empty(self::$accessToken2)) {
            self::$accessToken2 = $this->getTokenNonAdmin();
        }
    }

     /**
     * Test listing allowance payments for authenticated non-admin user.
     */
    public function test_allowance_payment_listing_authenticated_non_admin()
    {
        $this->ensureTokensPopulated();

        // Make a GET request to fetch allowance payments using the access token
        $response = $this->withToken(self::$accessToken2, 'Bearer')
            ->get(route('allowance-payments.index'), ['per_page' => 1]);
        $response->assertStatus(404);
    }

    /**
     * Test listing allowance payments for unauthenticated user.
     */
    public function test_allowance_payment_listing_unauthenticated_non_admin()
    {
        $this->ensureTokensPopulated();

        $response = $this->withToken(self::$accessToken2)
                    ->get(route('allowance-payments.index'), ['per_page' => 1]);
        $response->assertStatus(404);
    }


    /**
     * Test listing allowance payments for authenticated admin user.
     */
    public function test_allowance_payment_listing_authenticated_admin()
    {
        $this->ensureTokensPopulated();

        // Make a GET request to fetch allowance payments using the access token
        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->get(route('allowance-payments.index'), ['per_page' => 1]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' =>
            'success',
            'message',
            'data' => [
                '*' =>
                [
                'id',
                'title',
                'detail',
                'user_id',
                'created_at',
                'updated_at'
                ]               
            ],
            'pagination' => [
                '*' => 
                    'current_page',
                    'first_page_url',
                    'from',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to'                   
            ]
        ]);
    }

    /**
     * Common method for creating an allowance payment.
     *
     * @param array $formData The data to create the allowance payment.
     * @return \Illuminate\Testing\TestResponse The response from the creation request.
     */
    public function createAllowancePayment($formData)
    {
        $this->ensureTokensPopulated();

        $allowancePayment = $this->withToken(self::$accessToken, 'Bearer')
            ->post(route('allowance-payments.store'), $formData);
        return $allowancePayment;
    }

    public function test_create_allowance_payment()
    {
        
        $formData = $this->formData;
        $response = $this->createAllowancePayment($formData);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' =>
            'success',
            'message',
            'data' => [
                '*' =>
                [
                'id',
                'title',
                'detail',
                'user_id',
                'created_at',
                'updated_at'
                ]               
            ]
        ]);        
    }

    /**
     * Test retrieving a single allowance payment for authenticated admin user.
     */
    public function test_single_allowance_payment_authenticated_admin()
    {
        $this->ensureTokensPopulated();

        $this->setTestAllowancePayment();
        $allowance_data = $this->getTestAllowancePayment();

        // Make a GET request to fetch the single allowance payment using the access token
        $response = $this->withToken(self::$accessToken2, 'Bearer')
            ->get(route('allowance-payments.show', $allowance_data->id));
        $response->assertStatus(200);
    }

    protected function closeDBConnection()
    {
        unset($this->app['db']);
    }

}
