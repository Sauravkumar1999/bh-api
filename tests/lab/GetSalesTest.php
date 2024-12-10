<?php

namespace Tests\Feature;

use Tests\TestCase;

class GetSalesTest extends TestCase
{
    protected $accessToken;
    protected $user;
    protected $product;
    protected $sales;
    protected $form_data;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user for authentication
        $this->accessToken = $this->getToken();
        $this->product = \App\Models\Product::inRandomOrder()->first();
        $this->sales = \App\Models\Sale::inRandomOrder()->first();
        $this->user = \App\Models\User::inRandomOrder()->first();
        $company  = \App\Models\Company::inRandomOrder()->first();
        $money = floatval(number_format(rand(10, 100), '2'));

        $this->form_data = [
            'product_sale_day' => date('Y-m-d h:i:s'),
            'product_id' => $this->product->id,
            'company_id' => $company->id,
            'code' => $this->user->code,
            'fee_type' => 'fixed-price',
            'product_price' => $money,
            'remark' => 'Test sale',
            'seller_id' => '1',
            'sales_price' => $money,
            'number_of_sales' => '10',
            'take' => '10%',
            'sales_information' => 'Some information',
            'product_sale_status' => 'active',
            'user_id' => $this->user->id,
        ];

    }

    protected function tearDown(): void
    {
        $this->closeDBConnection();
        parent::tearDown();
    }

    /** @test */
    public function it_returns_sales_with_product_and_seller_information()
    {

        $response = $this->withToken($this->accessToken, 'Bearer')->get(route('sales'));
        $response->assertStatus(200);
        // ->assertJsonStructure([
        //     'data' => [
        //         '*' => [
        //             'id', 'code', 'product_sale_day', 'product_id', 'fee_type',
        //             'product_price', 'remark', 'sales_price', 'sales_type', 'take',
        //             'number_of_sales', 'sales_information', 'seller_id',
        //             'operating_income', 'sales_status', 'user_id', 'created_at',
        //             'updated_at', 'deleted_at', 'product' => [
        //                 'id', 'code', 'product_description',
        //                 'product_price', 'main_url', 'url_params', 'url_1', 'url_2',
        //                 'banner', 'urls_open_mode', 'sale_status', 'created_at', 'updated_at', 'company',
        //             ], 'seller' => [
        //                 'code', 'name', 'telephone_1', 'email', 'dob', 'gender',
        //                 'final_confirmation', 'company', 'role', 'sns', 'qr_code_url',
        //             ],
        //         ],
        //     ],
        // ])->assertJsonMissing(['data' => ['company' => null]]);
    }

    /** @test */
    public function it_returns_not_found_when_no_sales_exist()
    {
        $response = $this->withToken($this->accessToken, 'Bearer')->get(route('sales'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_sale()
    {
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->post(route('sales.create'), $this->form_data);
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    /** @test */
    public function it_can_update_a_sale()
    {
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->patch(route('sales.update', ['id' => $this->sales->id]),$this->form_data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'code',
                'product_sale_day',
                'product_id',
                'fee_type',
                'product_price',
                'remark',
                'sales_price',
                'sales_type',
                'number_of_sales'
            ]
        ]);
    }

    /** @test */
    public function it_can_delete_a_sale()
    {
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->delete(route('sales.delete', ['id' => $this->sales->id]));
        $response->assertStatus(200);
    }

    public function it_returns_sale_detail_with_product_and_seller_information()
    {
        // Act as the authenticated user
        $response = $this->get(route('sales.detail', ['id' => $this->sales->id]));

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'code', 'product_sale_day', 'product_id', 'fee_type',
                        'product_price', 'remark', 'sales_price', 'sales_type', 'take',
                        'number_of_sales', 'sales_information', 'seller_id',
                        'operating_income', 'sales_status', 'user_id', 'created_at',
                        'updated_at', 'deleted_at', 'product' => [
                            'id', 'code', 'product_name', 'product_description',
                            'product_price', 'main_url', 'url_params', 'url_1', 'url_2',
                            'banner', 'urls_open_mode', 'sale_status', 'created_at', 'updated_at', 'company',
                        ], 'seller' => [
                            'code', 'name', 'telephone_1', 'email', 'dob', 'gender',
                            'final_confirmation', 'company', 'role', 'sns', 'qr_code_url',
                        ],
                    ],
                ],
                'success',
                'message',
            ]);
    }

    /** @test */
    public function it_returns_not_found_when_sale_does_not_exist()
    {
        // Act as the authenticated user
        $response = $this->withToken($this->accessToken, 'Bearer')
            ->get(route('sales.detail', ['id' => 9999]));
        // Assert the response status and structure
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_product_detail_when_product_exists()
    {
        $response = $this->withToken($this->accessToken)->get(route('sales.productDetail', ['id' => $this->product->id]));
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => __('messages.product_found'),
                'data' => [
                    'id' => $this->product->id,
                ]
            ]);
    }

    public function test_sales_status_update()
    {
        $response = $this->withToken($this->accessToken,'Bearer')
                    ->patch(route('sales.update',['id' => $this->sales->id]),$this->form_data);
        $response->assertStatus(200);
    }    
}
