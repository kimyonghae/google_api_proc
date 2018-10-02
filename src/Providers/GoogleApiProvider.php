<?php
namespace GoogleApiProc\Providers;

use Illuminate\Support\ServiceProvider;

class GoogleApiProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/credentials.php' => config_path('credentials.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/credentials.php', 'credentials'
        );
    }

    public function provides()
    {
        return ['credentials'];
    }
}