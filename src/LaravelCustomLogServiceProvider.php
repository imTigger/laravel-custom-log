<?php
namespace Imtigger\LaravelCustomLog;

use Illuminate\Support\ServiceProvider;

class LaravelCustomLogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/custom-log.php' => config_path('custom-log.php')
        ], 'config');
        
        $this->publishes([
            __DIR__ . '/migrations/2018_07_07_000000_create_logs_table.php' => base_path('database/migrations/2018_07_07_000000_create_logs_table.php')
        ], 'migration');
    }

}