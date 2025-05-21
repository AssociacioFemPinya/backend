<?php

namespace Database\Factories;

use App\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return array_merge([
            'duration' => $this->faker->numberBetween(1, 160),
            'companions' => $this->faker->numberBetween(0, 1),
            'visibility' => $this->faker->numberBetween(0, 1),
            'type' => $this->faker->numberBetween(1, 3),
            'name' => $this->faker->sentence(3),
            'address' => $this->faker->streetAddress() . ', ' . $this->faker->city() . ', ' . $this->faker->country(),
        ],
            $this->dates(),
        );
    }

    public function open()
    {        
        return $this->state(function (array $attributes) {
            $open_date = Carbon::yesterday();
            $start_date = $this->faker->dateTimeBetween('+4 week', '+1 year');
            $close_date = $this->faker->dateTimeBetween('+1 week', $start_date);

            return [
                'start_date' => $start_date,
                'open_date' => $open_date,
                'close_date' => $close_date,
            ];
        });
    }

    public function past()
    {        
        return $this->state(function (array $attributes) {
            $open_date = $this->faker->dateTimeBetween('-1 year', '-1 month');
            $close_date = $this->faker->dateTimeBetween($open_date, '-1 month');
            $start_date = $this->faker->dateTimeBetween($close_date, '-1 month');
            
            return [
                'start_date' => $start_date,
                'open_date' => $open_date,
                'close_date' => $close_date,
            ];
        });
    }

    public function live()
    {        
        return $this->state(function (array $attributes) {
            $open_date = $this->faker->dateTimeBetween('-1 year', '-1 month');
            $close_date = $this->faker->dateTimeBetween($open_date, '-1 month');
            $start_date = $this->faker->dateTimeBetween(Carbon::now()->subHour(), Carbon::now());

            
            return [
                'start_date' => $start_date,
                'open_date' => $open_date,
                'close_date' => $close_date,
                'duration' => 4*60, // 4 hours
            ];
        });
    }

    public function today()
    {        
        return $this->state(function (array $attributes) {
            $open_date = $this->faker->dateTimeBetween('-1 year', '-1 month');
            $close_date = $this->faker->dateTimeBetween($open_date, '-1 month');
            $start_date = $this->faker->dateTimeBetween(Carbon::now(), Carbon::now()->addHours(1));

            
            return [
                'start_date' => $start_date,
                'open_date' => $open_date,
                'close_date' => $close_date,
                'duration' => 30, // 4 hours
            ];
        });
    }

    public function future()
    {        
        return $this->state(function (array $attributes) {
            $open_date = $this->faker->dateTimeBetween('+1 week', '+1 year');
            $close_date = $this->faker->dateTimeBetween($open_date, '+1 year');
            $start_date = $this->faker->dateTimeBetween($close_date, '+1 year');
            
            return [
                'start_date' => $start_date,
                'open_date' => $open_date,
                'close_date' => $close_date,
            ];
        });
    }

    private function dates(): array
    {
        $start_date = $this->faker->dateTimeThisYear('+8 months');
        $open_date= $this->faker->dateTimeBetween(Carbon::parse($start_date)->subDays( $this->faker->numberBetween(7, 30)), $start_date);
        $close_date = $this->faker->dateTimeBetween($open_date, $start_date);

        return [
            'start_date' => $start_date,
            'open_date' => $open_date,
            'close_date' => $close_date,
        ];
    }
}
