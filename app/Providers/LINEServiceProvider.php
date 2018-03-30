<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\LINE\LINEBot;

class LINEServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('line', function() {
			return new LINEBot();
		});
    }
}
