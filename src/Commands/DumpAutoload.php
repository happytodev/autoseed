<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

/**
 * Originally, this class was created by
 * Marek Skiba (https://stackoverflow.com/users/6729812/marek-skiba)
 * Source : https://stackoverflow.com/a/42555169/4182752
 */
class DumpAutoload extends Command
{

    protected $composer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump-autoload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate framework autoload files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->composer->dumpAutoloads();
        $this->composer->dumpOptimized();


        return 0;
    }
}
