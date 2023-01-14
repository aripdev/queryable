<?php


namespace Aripdev\Queryable;

use Illuminate\Support\ServiceProvider;

class QueryableServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('xheader', function () {
            return new class
            {
                public $headers = [];
            };
        });
    }
}
