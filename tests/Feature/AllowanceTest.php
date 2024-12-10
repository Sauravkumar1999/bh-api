<?php

namespace Tests\Feature;

use Tests\TestCase;

class AllowanceTest extends TestCase
{
    private $form_data;
    private $money;
    protected static $test_allowance;
    protected static $token;
    protected static $wasSetup = false;

    public function setUp(): void
    {
        parent::setup();
        $this->sharedSetUp();
    }

    /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {
        // Get tokens for admin and normal user
        $this->setTestAllowance();
        self::$test_allowance = $this->getTestAllowance();
        self::$token = $this->getToken();

        $this->money = floatval(number_format(rand(10, 100), '2'));
        $this->form_data = [
            'payment_month' => date('m'),
            'member_id' => rand(20, 100),
            'referral_bonus' => $this->money,
            'commission' => $this->money,
            'headquarters_representative_allowance' => rand(1, 100),
            'organization_division_allowance' => $this->money,
            'other_allowances'  => $this->money,
            'income_tax' => $this->money,
            'resident_tax'  => $this->money,
            'year_end_settlement' => $this->money,
            'other_deductions_1' => $this->money,
            'other_deductions_2'  => $this->money,
            'total_deduction' => $this->money,
            'total_before_tax'  => $this->money,
            'policy_allowance' => $this->money,
            'deducted_amount_received'  => $this->money,
        ];

    }

    public function test_allowances_creation()
    {

        $response = $this->withToken(self::$token, 'Bearer')
            ->post(route('create_allowance'), $this->form_data);
        $response->assertStatus(200);
        $this->checkReturnedDataStructure($response);
    }

    public function test_allowances_updation()
    {

        $response = $this->withToken(self::$token, 'Bearer')
            ->put(route('update_allowance', ["id" => self::$test_allowance->id]), $this->form_data);
        $response->assertStatus(200);
        $this->checkReturnedDataStructure($response);
    }

    public function test_allowances_listing()
    {
        $response = $this->withToken(self::$token, 'Bearer')
            ->get(route('allowances'));
        $response->assertStatus(200);
    }

    public function test_get_allowances()
    {

        $response = $this->withToken(self::$token, 'Bearer')
            ->get(route('view_allowances', ["id" => self::$test_allowance->id]));
        $response->assertStatus(200);
        $this->checkReturnedDataStructure($response);
    }

    public function test_allowances_deletion()
    {
        $response = $this->withToken(self::$token, 'Bearer')
            ->delete(route('delete_allowance', ["id" => self::$test_allowance->id]));
        $response->assertStatus(200);
    }

    public function checkReturnedDataStructure($response)
    {
        return  $response->assertJsonStructure([
            'data' => ['*' =>
                'id',
                'member_id',
                'code',
                'name',
                'position',
                'payment_month',
                'commision',
                'referral_bonus',
                'total_before_tax',
                'income_tax',
                'resident_tax',
                'year_end_settlement',
                'other_deductions_1',
                'other_deductions_2',
                'total_deduction',
                'deducted_amount_received',
                'first_registration_date',
                'last_modified_date',
                'extra_pay' => [
                    'headquarters_representative',
                    'organizational_division',
                    'policy',
                    'other_allowance'
                ]
            ]
        ]);
    }
}
