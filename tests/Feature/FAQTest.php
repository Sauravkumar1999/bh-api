<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Tests\TestCase;

class FAQTest extends TestCase
{
    private $form_data;
    protected $faker;
    private $test_faq;
    protected static $accessToken;

    public function setUp(): void
    {
        parent::setUp();
        $this->sharedSetUp();

    }

    /*
    * Shared setup that runs only once before all tests.
    */
    protected function sharedSetUp(): void
    {
        $this->faker = Faker::create('en_US');

        self::$accessToken = $this->getToken();

        $this->setTestFaq();
        $this->test_faq = $this->getTestFaq();

        $this->form_data = [
            'title' => $this->faker->text(),
            'description' => $this->faker->text(),
            'status'      => 1
        ];
    }

    public function test_create_new_faq()
    {

        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->post(route('create_faq'), $this->form_data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' =>
            [
                'id',
                'title',
                'description',
                'user_id',
                'status',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_update_faq()
    {

        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->put(route('update_faq', ['id' => $this->test_faq->id]), $this->form_data);
        $response->assertStatus(200);
    }

    public function test_view_existing_faq()
    {
        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->get(route('view_faq', ['id' => $this->test_faq->id]));
        $response->assertStatus(200);
    }

    public function test_list_all_faqs_items()
    {
        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->get(route('faq'));

        $response->assertStatus(200);
    }

    public function test_faq_deletion()
    {
        $response = $this->withToken(self::$accessToken, 'Bearer')
            ->delete(route('delete_faq', ['id' => $this->test_faq->id]));
        //$response->assertStatus(200);
    }
}
