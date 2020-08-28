<?php

namespace Happytodev\Autoseed;

use Illuminate\Support\ServiceProvider;

class AutoseedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->make('Happytodev\Autoseed\DbController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        include __DIR__ . '/routes.php';
    }
}
