<?php

namespace App\Observers;

use App\Generator\RandomStringGenerator;
use App\UpcomingMatch;

class UpcomingMatchObserver
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
     * Handle the upcoming match "creating" event.
     *
     * @param \App\UpcomingMatch $upcomingMatch
     *
     * @return void
     */
    public function creating(UpcomingMatch $upcomingMatch)
    {
        $upcomingMatch->id = $this->generator->generate();
    }
}
