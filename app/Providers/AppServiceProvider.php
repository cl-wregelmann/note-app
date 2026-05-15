<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (Schema::hasTable('users')) {
            User::firstOrCreate(
                ['email' => 'local@localhost'],
                ['name' => 'Local User', 'password' => '']
            );
        }
    }
}
