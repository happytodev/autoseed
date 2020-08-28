<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Autoseed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoseed:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch automatic generation of seeds based on your database structure';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
