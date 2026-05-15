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
        if (Schema::hasTable('labels')) {
            View::share('labels', Label::orderBy('name')->get());
        }
    }
}
