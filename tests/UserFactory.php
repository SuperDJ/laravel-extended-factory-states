<?php

namespace SuperDJ\LaravelExtendedFactoryStates\Tests;

use SuperDJ\LaravelExtendedFactoryStates\Factory;

class UserFactory extends Factory
{
    protected $model           = User::class;
    protected $collidingStates = [
        'employee' => [ 'customer' ],
        'seller'   => [ 'customer' ]
    ];
    protected $requiredStates  = [
        'seller' => [ 'employee' ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'              => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => bcrypt( 'password' ),
            'remember_token'    => \Illuminate\Support\Str::random( 10 ),
        ];
    }

    public function employee(): Factory
    {
        return $this->state( [
            'employee' => 1
        ], 'employee' );
    }

    public function customer(): Factory
    {
        return $this->state( [
            'customer' => 1
        ], 'customer' );
    }

    public function seller(): Factory
    {
        return $this->state( [
            'seller' => 1
        ], 'seller' );
    }
}