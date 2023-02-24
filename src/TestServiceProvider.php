<?php

namespace Obrainwave\AccessTree;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Calculator;

class TestServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('calculator', function($app) {
            return new Calculator();
        });

        
    }

    public function boot()
    {
        
    }
}
