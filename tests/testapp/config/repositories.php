<?php
use \CroudTech\RepositoriesTests\Repositories\Contracts\UserRepositoryContract;
use \CroudTech\RepositoriesTests\Repositories\UserRepository;
use \CroudTech\RepositoriesTests\Repositories\UserApiRepository;
use \CroudTech\RepositoriesTests\Transformers\UserTransformer;
use \CroudTech\RepositoriesTests\Controllers\UserController;
use \CroudTech\RepositoriesTests\Controllers\UserApiController;

return [
    'repositories' => [
        UserRepositoryContract::class => UserRepository::class,
    ],

    'repository_transformers' => [
        UserRepository::class => UserTransformer::class,
        UserApiRepository::class => UserTransformer::class,
    ],

    'contextual_repositories' => [
        UserController::class => UserRepositoryContract::class,
        UserApiController::class => UserApiRepository::class,
    ],
];
