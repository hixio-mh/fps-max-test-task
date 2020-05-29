<?php

namespace App\Providers;

use App\Generator\RandomStringGenerator;
use App\Generator\Uuid4Generator;
use App\League;
use App\Observers\LeagueObserver;
use App\Observers\TeamObserver;
use App\Observers\UpcomingMatchObserver;
use App\Team;
use App\UpcomingMatch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RandomStringGenerator::class, Uuid4Generator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();
    }

    private function registerObservers()
    {
        League::observe(LeagueObserver::class);
        Team::observe(TeamObserver::class);
        UpcomingMatch::observe(UpcomingMatchObserver::class);
    }
}
