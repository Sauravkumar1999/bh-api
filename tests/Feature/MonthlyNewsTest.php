<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MonthlyNewsTest extends TestCase
{
    use WithFaker;

    protected $accessToken;
    protected $formData;
    protected $updateData;

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
        $this->formData = [
            'detail' => $this->faker->paragraph(5),
            'form' => $this->faker->sentence(2),
            'posting_date' => $this->faker->date()
        ];
        $this->updateData = [
            'detail' => $this->faker->paragraph(6),
            'form' => $this->faker->sentence(3),
            'posting_date' => $this->faker->date()
        ];
    }

    public function test_monthly_news_crud()
    {
        

        //test Create
        $create = $this->withToken($this->accessToken)->post(route('monthlyNews.create'), $this->formData)->assertOk()->json();
        $id = $create['data']['id'];
        $this->assertEquals($create['success'], true);

        //test Update
        $this->withToken($this->accessToken)->put(route('monthlyNews.update', $id), $this->updateData)->assertOk();
        $this->assertDatabaseHas('monthly_news', ['detail' => $this->updateData['detail'], 'form' => $this->updateData['form']]);

        //test delete
        $this->withToken($this->accessToken)->delete(route('monthlyNews.delete', $id))->assertOk();
        $this->assertDatabaseMissing('monthly_news', ['id' => $id]);
    }
}
