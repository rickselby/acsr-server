<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class UserNamesCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'users:names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user names from the discord guild';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(UserService $userService)
    {
        $userService->updateNames();
    }

}
