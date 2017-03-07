<?php
use \Croud\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use \Croud\RepositoryTests\Repositories\UserRepository;

return [
    'providers' => [
        Illuminate\Cache\CacheServiceProvider::class,
        Croud\Repositories\Providers\RepositoryServiceProvider::class,
    ],

    'repositories' => [
        UserRepositoryContract::class => UserRepository::class,
    ],
];
