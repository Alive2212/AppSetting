<?php

namespace   Alive2212\AppSetting\Console;

use Alive2212\AppSetting\AppSetting;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AppSetting:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To clear all setting what stored in database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $appSetting = new AppSetting();
        $appSetting->clearDb();
    }
}
