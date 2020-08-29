<?php
namespace Happytodev\Autoseed;

use Happytodev\Autoseed\Commands\Autoseed;
use Happytodev\Autoseed\Commands\DumpAutoload;
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
