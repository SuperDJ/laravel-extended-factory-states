<?php

namespace SuperDJ\LaravelExtendedFactoryStates\Tests;

class FactoryTest extends TestCase
{
    public function test_factory_states_can_collide(): void
    {
        $this->expectExceptionMessage('State customer can not be combined with employee');
        User::factory()->employee()->customer()->create();
        $this->assertCount(0, User::count());
    }

    public function test_factory_states_can_be_required(): void
    {
        $this->expectExceptionMessage('State ');
        User::factory()->seller()->create();
    }
}