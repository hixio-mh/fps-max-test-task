<?php

namespace App\Observers;

use App\Generator\RandomStringGenerator;
use App\Team;

class TeamObserver
{
    /**
     * @var RandomStringGenerator
     */
    private RandomStringGenerator $generator;

    public function __construct(RandomStringGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Handle the team "creating" event.
     *
     * @param \App\League $team
     *
     * @return void
     */
    public function creating(Team $team)
    {
        $team->id = $this->generator->generate();
    }
}
