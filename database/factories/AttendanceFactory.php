<?php

namespace Database\Factories;

use App\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => $this->faker->numberBetween(1, 3),
            'companions' => $this->faker->numberBetween(1, 5)
        ];
    }
}
