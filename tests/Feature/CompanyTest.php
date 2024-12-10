<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Factory as Faker;

class CompanyTest extends TestCase
{
    private static $test_company;
    private $create_form_data;
    protected $faker;
    protected static $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();
            
    }

     /**
     * Shared setup that runs only once before all tests.
     */
    protected function sharedSetUp(): void
    {

        $this->setTestCompany();
        self::$test_company = $this->getTestCompany();

        self::$token = $this->getToken();
        
        $this->faker = Faker::create('en_US');

        $this->create_form_data = [
            "name" => $this->faker->name(),
            "url" => "example.com",
            "business_name" => "{$this->faker->name()} Technology Ltd.",
            "representative_name" => $this->faker->name(),
            "registration_number" => $this->faker->numerify(str_repeat('#', 10)),
            "address" => $this->faker->address(),
            "scope_of_disclosure" => $this->faker->text(),
            "registration_date" => date('Y/m/d')
        ];

    }

    public function test_verify_companies_code()
    {
        $verify = $this->withToken(self::$token, 'Bearer')
            ->get(route('companies', ["code" => self::$test_company->code]));
        $verify->assertStatus(200);
    }

    public function test_create_new_company()
    {
        $response = $this->withToken(self::$token, 'Bearer')
            ->post(route('create_company'), $this->create_form_data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' =>
            'success',
            'message',
            'data' => [
                
                    'id',
                    'code',
                    'name',
                    'status',
                    'url',
                    'business_name',
                    'representative_name',
                    'registration_number',
                    'address',
                    'scope_of_disclosure',
                    'logo',
                    'contract',
                    'registration_date',
                    'created_at'
                
            ]
                ]);
    }

    public function test_create_new_company_with_only_required_data()
    {
        $form_data = ['name' => $this->faker->name()];
        $response = $this->withToken(self::$token, 'Bearer')
            ->post(route('create_company'), $form_data);
        $response->assertStatus(200);
        $data1 = $response->original;
        // assert that the company list first data is equal to the new created company
        $response = $this->withToken(self::$token,'Bearer')
                    ->get(route('companies'),['per_page' => 1]);
        $response = $response->decodeResponseJson();
        $this->assertEquals($data1['data']->id,$response["data"][0]['id']);        
    }

    public function test_update_company_data()
    {
        $update_company =  $this->withToken(self::$token, 'Bearer')
            ->put(route('update_company', ["company_code" => self::$test_company->code]), $this->create_form_data);
            $data1 = $update_company->original;
        $update_company->assertStatus(200);
    }

    public function test_companies_list_data()
    {
        $response = $this->withToken(self::$token, 'Bearer')
            ->get(route('companies'));
        $response->assertStatus(200);
        $this->assertResponse($response);
    }

    public function test_companies_soft_deletion()
    {
        $verify = $this->withToken(self::$token, 'Bearer')
            ->delete(route('delete_company', ["company_code" => self::$test_company->code]));
        $verify->assertStatus(200);
    }

    private function assertResponse($response)
    {
        $response->assertJsonStructure([
            '*' =>
            'success',
            'message',
            'data' => [
                [
                    'id',
                    'code',
                    'name',
                    'status',
                    'url',
                    'business_name',
                    'representative_name',
                    'registration_number',
                    'address',
                    'scope_of_disclosure',
                    'logo',
                    'contract',
                    'registration_date',
                    'created_at'
                ]
            ]
                ]);
    }
}
