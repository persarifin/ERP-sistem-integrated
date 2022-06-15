<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;

class UniqueCompanyProvider extends ServiceProvider
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
        Validator::extend('search', function ($attribute, $value, $parameters)
        {
            list($table) = $parameters;
            return DB::table($table)->where(['id'=> $value,'company_id' => Auth::user()->company_id])->count() > 0;
        },':attribute not found');
    }
}
