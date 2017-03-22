<?php

return [
    /**
     * This path is used when generating repositories
     */
    'repositories_path' => app_path('Repositories'),

    /**
     * This path is used when generating repository contracts
     */
    'contracts_path' => app_path('Repositories/Contracts'),

    /**
     * Register your repositories here as follows
     *
     * Interface => ConcreteClass
     *
     * For example:
     *
     * UserRepositoryContract::class => UserRepository::class,
     */
    'repositories' => [

    ],

    /**
     * Register the transformers that should be bound to the repositories
     *
     * For example:
     *
     * UserRepository::class => UserTransformer::class,
     * UserApiRepository::class => UserTransformer::class,
     *
     */
    'repository_transformers' => [

    ],

    /**
     * Register the contextual repositories here
     *
     * For example:
     *
     * UserController::class => UserRepositoryContract::class,
     * UserApiController::class => UserApiRepository::class,
     */
    'contextual_repositories' => [

    ],
];
