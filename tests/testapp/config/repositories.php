<?php
use \CroudTech\Repositories\TestApp\Repositories\Contracts\UserRepositoryContract;
use \CroudTech\Repositories\TestApp\Repositories\UserRepository;
use \CroudTech\Repositories\TestApp\Repositories\UserApiRepository;
use \CroudTech\Repositories\TestApp\Transformers\UserTransformer;
use \CroudTech\Repositories\TestApp\Controllers\UserController;
use \CroudTech\Repositories\TestApp\Controllers\UserApiController;

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
