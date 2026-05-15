<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        User::firstOrCreate(
            ['email' => 'local@localhost'],
            ['name' => 'Local User', 'password' => '']
        );
    }
}
