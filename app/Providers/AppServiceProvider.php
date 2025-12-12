<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\MongoCategory;
use Illuminate\Support\Facades\View;

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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('vendor.pagination.bootstrap-4');
        View::composer('*', function ($view) {
        $category = MongoCategory::all();
        $view->with('category', $category);
    });
    }
}
