<?php

namespace Database\Factories;

use App\Casteller;
use Illuminate\Database\Eloquent\Factories\Factory;

class CastellerFactory extends Factory
{
    protected $model = Casteller::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    private function namesAndGender(): array
    {
        $gender = $this->faker->numberBetween(0, 1);
        if ($gender > 0) {
            $name = $this->faker->firstName('male');
        } else {
            $name = $this->faker->firstName('female');
        }

        return [
            'gender' => $gender,
            'name' => $name,
            'last_name' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'alias' => $name . 'n' . $this->faker->numberBetween(0, 9),
        ];
    }

    public function definition()
    {
        return array_merge(
            [
                'email' => $this->faker->safeEmail(),
                'email2' => null,
                'national_id_type' => 'dni',
                'nationality' => 'nationality',
                'national_id_number' => null,
                'birthdate' => $this->faker->dateTimeThisCentury(),
                'mobile_phone' => $this->faker->phoneNumber(),
                'phone' => $this->faker->phoneNumber(),
                'emergency_phone' => $this->faker->phoneNumber(),
                'address' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'postal_code' => '080' . $this->faker->numberBetween(10, 42),
                'province' => $this->faker->country(),
                'comarca' => null,
                'country' => null,
                'comments' => null,
                'weight' => $this->faker->numberBetween(50, 100),
                'height' => $this->faker->numberBetween(130, 170),
                'status' => $this->faker->numberBetween(1, 4),
            ],
            $this->namesAndGender(),
        );
    }
}
