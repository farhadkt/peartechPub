<?php

namespace App\Providers;

use App\Setting;
use App\User;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        view()->composer('layouts.app.content-header', function($view){
            $vc_commission_const = Setting::key('commission');
            $view->with('vc_commission_const', $vc_commission_const);
        });
    }
}
