<?php

namespace Database\Factories;

use App\Colla;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollaFactory extends Factory
{
    protected $model = Colla::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'shortname' => $this->faker->companySuffix(),
        ];
    }
}
