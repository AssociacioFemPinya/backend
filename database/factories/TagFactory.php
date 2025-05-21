<?php

namespace Database\Factories;

use App\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'value' => $this->faker->sentence(1),
        ];
    }

    public function event()
    {        
        return $this->state(function (array $attributes) {            
            return [
                'type' => "EVENTS",
            ];
        });
    }

    public function casteller()
    {        
        return $this->state(function (array $attributes) {            
            return [
                'type' => "CASTELLERS",
            ];
        });
    }
}
