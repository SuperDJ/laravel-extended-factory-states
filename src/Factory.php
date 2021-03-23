<?php

namespace SuperDJ\LaravelExtendedFactoryStates;

use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Factory extends BaseFactory
{
    /**
     * The states that were called.
     *
     * @var string[]|null
     */
    protected $calledStates = null;

    /**
     * The states that will collide.
     *
     * @var string[]|null
     */
    protected $collidingStates = null;

    /**
     * States that must be combined
     *
     * @var string[]|null
     */
    protected $requiredStates = null;

    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null,
        ?array $calledStates = null,
        ?array $collidingStates = null,
        ?array $requiredStates = null
    )
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $this->calledStates = $calledStates;
        $this->collidingStates = $collidingStates;
        $this->requiredStates = $requiredStates;
    }

    /**
     * Create a new instance of the factory builder with the given mutated properties.
     *
     * @param  array  $arguments
     * @return static
     */
    protected function newInstance(array $arguments = [])
    {
        return new static(...array_values(array_merge([
            'count' => $this->count,
            'states' => $this->states,
            'has' => $this->has,
            'for' => $this->for,
            'afterMaking' => $this->afterMaking,
            'afterCreating' => $this->afterCreating,
            'connection' => $this->connection,
            'calledStates' => $this->calledStates,
            'collidingStates' => $this->collidingStates,
            'requiredStates' => $this->requiredStates
        ], $arguments)));
    }

    public function state($state, ?string $calledState = null)
    {
        if ($calledState) {
            $this->checkStateCollides($calledState);

            $this->calledStates[] = $calledState;
        }

        return parent::state($state);
    }

    public function create( $attributes = [], ?Model $parent = null )
    {
        $this->checkRequiredStates();

        return parent::create( $attributes, $parent );
    }

    public function make( $attributes = [], ?Model $parent = null )
    {
        $this->checkRequiredStates();

        return parent::make( $attributes, $parent );
    }

    /**
     * @param string $newState
     *
     * @return void
     * @throws \Exception
     */
    private function checkStateCollides(string $newState): void
    {
        if (
            is_array($this->collidingStates)
            && count($this->collidingStates) > 0
            && is_array($this->calledStates)
            && count($this->calledStates) > 0
        ) {
            foreach ($this->calledStates as $calledState) {
                if (in_array($newState, $this->collidingStates[$calledState])) {
                    throw new \Exception('State '.$newState.' can not be combined with '.$calledState);
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function checkRequiredStates(): void
    {
        if(
            is_array($this->requiredStates)
            && count($this->requiredStates) > 0
            && is_array($this->calledStates)
            && count($this->collidingStates) > 0
        )
        {
            foreach($this->calledStates as $calledState)
            {
                if(!in_array($calledState, $this->requiredStates[$calledState]))
                {
                    throw new \Exception('State '.$calledState.' needs to be combined with any of: '.implode(', ', $this->requiredStates[$calledState]));
                }
            }
        }
    }
}