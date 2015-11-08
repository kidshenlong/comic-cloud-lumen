<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot(){
        //if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            //DB::statement(DB::raw('PRAGMA foreign_keys = ON'));
            //DB::statement('PRAGMA foreign_keys = ON');
        //}
    }
}
