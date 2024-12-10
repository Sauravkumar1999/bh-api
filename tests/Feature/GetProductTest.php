<?php

namespace Tests\Feature;

use Tests\TestCase;

class GetProductTest extends TestCase
{
    protected $accessToken;

    /**
     * Set up before each test.
     */
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
     *
     *
     * @return void
     */
    public function test_get_product_list_found()
    {
        $response = $this->withToken($this->accessToken)->get(route('products.index', ['per_page' => 1]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [[
                'id',
                'code',
                'product_name',
                'product_description',
                'product_price',
                'main_url',
                'url_params',
                'url_1',
                'url_2',
                'banner',
                'urls_open_mode',
                'sale_status',
                'created_at',
                'updated_at',
                'company'
            ]]
        ]);
    }

    public function test_get_product_list_not_found()
    {
        // per page must be interger given string
        $code = "C00A";
        $response = $this->withToken($this->accessToken)->get(route('products.index', ['code' => $code]));
        $response->assertStatus(404);
    }
}
