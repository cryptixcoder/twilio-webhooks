<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

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
        $this->configureResponseMacros();
    }

    protected function configureResponseMacros()
    {
        Response::macro('twiml', function ($value) {
            return Response::make($value, 200, [
                'Content-Type' => 'text/xml'
            ]);
        });
    }
}
