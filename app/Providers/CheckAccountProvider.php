<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use DB;

class CheckAccountProvider extends ServiceProvider
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
        Validator::extend('check_users', function ($attribute, $value, $parameters)
        {
            list($table) = $parameters;
            return DB::table($table)->where('email' , $value)->orWhere('phone',$value)->orWhere('username',$value)->count() > 0;
        },':attribute not found, please enter another valid account address');
    }
}
