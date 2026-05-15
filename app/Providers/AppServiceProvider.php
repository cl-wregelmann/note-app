<?php

namespace App\Providers;

use App\Models\Label;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (config('database.default') === 'sqlite') {
            $path = config('database.connections.sqlite.database');
            if ($path !== ':memory:' && ! file_exists($path)) {
                touch($path);
            }
        }

        if (Schema::hasTable('labels')) {
            View::share('labels', Label::orderBy('name')->get());
        }
    }
}
