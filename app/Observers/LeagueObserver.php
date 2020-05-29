<?php

namespace App\Observers;

use App\Generator\RandomStringGenerator;
use App\League;

class LeagueObserver
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
     * Handle the league "creating" event.
     *
     * @param \App\League $league
     *
     * @return void
     */
    public function creating(League $league)
    {
        $league->id = $this->generator->generate();
    }
}
