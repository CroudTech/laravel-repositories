<?php
use \CroudTech\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use \CroudTech\RepositoryTests\Repositories\UserRepository;
use \CroudTech\RepositoryTests\Transformers\UserTransformer;

return [
    'providers' => [
        Illuminate\Cache\CacheServiceProvider::class,
        CroudTech\Repositories\Providers\RepositoryServiceProvider::class,
    ],

    'repositories' => [
        UserRepositoryContract::class => UserRepository::class,
    ],

    'repository_transformers' => [
        UserRepository::class => UserTransformer::class,
    ],
];
