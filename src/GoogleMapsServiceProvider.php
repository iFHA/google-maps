<?php

namespace BeeDelivery\GoogleMaps;

use Illuminate\Support\ServiceProvider;

class GoogleMapsServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/googlemaps.php', 'googlemaps');

        // Register the main class to use with the facade
        $this->app->singleton('googlemaps', function () {
            return new GoogleMaps;
        });
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/googlemaps.php' => config_path('googlemaps.php'),
            ], 'config');            
        }
    }

    /**
    * Get the services provided by the provider.
    *
    * @return array
    */
   public function provides()
   {
       return ['googlemaps'];
   }
}
