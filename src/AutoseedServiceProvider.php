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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Autoseed::class,
                DumpAutoload::class,
            ]);
        }
    }
}
