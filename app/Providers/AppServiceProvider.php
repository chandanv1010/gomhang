<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Carbon\Carbon;
use App\Http\ViewComposers\MenuComposer;
use App\Http\ViewComposers\CartComposer;
use App\Http\ViewComposers\CustomerComposer;
use App\Models\Language;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
       
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // foreach($this->bindings as $key => $val)
        // {
        //     $this->app->bind($key, $val);
        // }

        // $this->app->register(RepositoryServiceProvider::class);

        /** @noinspection PhpUndefinedMethodInspection */


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // $this->publishes([
        //     resource_path('vendor/frontend/resources') => public_path('vendor/frontend'),
        //     resource_path('vendor/backend') => public_path('vendor/backend'),
        // ], 'assets');

        if (app()->environment(['local', 'testing'])) {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        }

        
        $locale = app()->getLocale(); // vn en cn
        $language = Language::where('canonical', $locale)->first();

        Validator::extend('custom_date_format', function($attribute, $value, $parameters, $validator){
            return Datetime::createFromFormat('d/m/Y H:i', $value) !== false;
        });

        Validator::extend('custom_after', function($attribute, $value, $parameters, $validator){
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $validator->getData()[$parameters[0]]);
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $value);
            
            return $endDate->greaterThan($startDate) !== false;
        });


        view()->composer(['frontend.*', 'mobile.*'], function($view) use ($language){
            $composerClasses = [
                MenuComposer::class,
                CartComposer::class,
                \App\Http\ViewComposers\SystemComposer::class,
                \App\Http\ViewComposers\LanguageComposer::class,
            ];

            foreach($composerClasses as $key => $val){
                $composer = app()->make($val, ['language' => $language->id]);
                $composer->compose($view);
            }
        });

      

     

        Schema::defaultStringLength(191);
    }
}
