<?php
use \CroudTech\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use \CroudTech\RepositoryTests\Repositories\UserRepository;

return [
    'providers' => [
        Illuminate\Cache\CacheServiceProvider::class,
        CroudTech\Repositories\Providers\RepositoryServiceProvider::class,
    ],

    'repositories' => [
        UserRepositoryContract::class => UserRepository::class,
    ],
];
