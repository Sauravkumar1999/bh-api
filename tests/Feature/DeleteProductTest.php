<?php

namespace Tests\Feature;

use App\Models\Product;
use Faker\Factory as Faker;
use Tests\TestCase;

class DeleteProductTest extends TestCase
{
    protected static $accessToken;
    protected static $testProduct;

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
        $this->faker = Faker::create();
        self::$accessToken = $this->getToken();

        $this->setTestProduct();
        self::$testProduct = $this->getTestProduct();

    }

    /**
     * Test successful delete.
     *
     * @return void
     */
    public function test_successful_delete()
    {
        $response = $this->delete_product(self::$testProduct->id);
        $response->assertStatus(200);
    }

    /**
     * Test unsuccessful delete.
     *
     * @return void
     */
    public function test_unsuccessful_delete()
    {
        $id = 9999999; //invalid id
        $response = $this->delete_product($id);
        $response->assertStatus(401);
    }

    public function delete_product($id)
    {
        return $this->withToken(self::$accessToken)->delete(route('products.delete', ['id' => $id]));
    }
}
