<?php
namespace CroudTech\Repositories\Providers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use \CroudTech\Repositories\Contracts\RepositoryContract;
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
        $this->publishes([
            dirname(dirname(__DIR__)).'/config/repositories.php' => config_path('repositories.php'),
        ]);

        foreach (config('repositories.repositories', []) as $respository_contract => $repository) {
            $this->app->bind($respository_contract, $repository);
        }

        foreach (config('repositories.repository_transformers', []) as $transformer_contract => $transformer) {
            $this->app->bind($transformer, $transformer);
        }

        foreach (config('repositories.repository_transformers', []) as $repository_class => $transformer) {
            $this->app->when($repository_class)
                ->needs(TransformerContract::class)
                ->give(function () use ($transformer) {
                    return $this->app->make($transformer);
                });
        }

        foreach (config('repositories.contextual_repositories', []) as $controller => $repository) {
            $this->app->when($controller)
              ->needs(RepositoryContract::class)
              ->give($repository);
        }

        $this->commands([
            \CroudTech\Repositories\Console\Commands\CreateRepository::class,            
        ]);
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
