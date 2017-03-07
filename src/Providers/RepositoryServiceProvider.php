<?php
namespace Croud\Repositories\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any repositories.
     *
     * @return void
     */
    public function boot()
    {
        foreach (config('app.repositories') as $respository_contract => $repository) {
            $this->app->bind($respository_contract, $repository);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
