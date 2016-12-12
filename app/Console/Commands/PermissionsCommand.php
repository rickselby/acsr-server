<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class PermissionsCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'permissions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add required permissions for the app.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach(config('permissions') AS $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if (!$permission || !$permission->exists) {
                Permission::create(['name' => $permissionName]);
            }
        }
    }

}
