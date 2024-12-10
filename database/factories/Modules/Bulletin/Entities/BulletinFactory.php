<?php

namespace Database\Factories\Modules\Bulletin\Entities;
use App\Models\Bulletin;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\User\Entities\Bulletin>
 */
class BulletinFactory extends Factory
{
    use RefreshDatabase;
    protected $model = Bulletin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        try {
            $this->faker = Faker::create('en_US');
            $data = [
                'title'         =>  Str::random(6),
                'distinguish'   => $this->faker->randomElement(['general', 'important']),
                'attachment'    => null, // Assuming you want this to be null
                'permission'    => json_encode([
                    rand(0,10),
                    rand(0,10),
                    rand(0,10),
                ]),
                'content'       => $this->faker->paragraph(),
                'user_id'       => User::factory()->create()->id,
                'view_count'    => $this->faker->randomNumber(),
                'created_at'    => now(),
                'updated_at'    => now(),
                'deleted_at'    => null,
            ];
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function withUser($userId)
    {
        return  $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }
}
