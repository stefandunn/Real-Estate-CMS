<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TraitsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
        // Register traits
        foreach (glob(app_path().'/Traits/*.php') as $filename){
            require_once($filename);
        }
    }
}
