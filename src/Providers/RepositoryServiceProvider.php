<?php
namespace CroudTech\Repositories\Providers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use \Illuminate\Support\ServiceProvider;

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

        foreach (config('app.repository_transformers') as $transformer_contract => $transformer) {
            $this->app->bind($transformer, $transformer);
        }

        foreach (config('app.repository_transformers') as $repository_class => $transformer) {
            $this->app->when($repository_class)
              ->needs(TransformerContract::class)
              ->give(function () use($transformer) {
                  return $this->app->make($transformer);
              });
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
