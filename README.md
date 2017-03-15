# laravel-repositories

[![Build Status](https://travis-ci.org/CroudSupport/laravel-repositories.svg?branch=master)](https://travis-ci.org/CroudSupport/laravel-repositories)

## Use Repositories with the Laravel framework

### Setup

This package uses dependency injection via the Laravel container to inject repositories into controllers or other classes that may require them.

#### Register Service Provider

Add service provider to the 'providers' section of your app config.


```
'providers' => [
    ...
    CroudTech\Repositories\Providers\RepositoryServiceProvider::class,
    ...
],
```

Add the repository definitions into your resources config file as follows:

```
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
```

The 'repositories' defined the concrete implementation of each repository contract.

The 'repository_transformers' defines the transformer classes that should be injected into each repository.

The 'contextual_repositories' Defines contextual repositories. This is useful where different controllers will need different implementations of the same contract.

To specify the repository required by a controller just add it's contract or classname into the construct method of the controller.

For example to inject the UserRespsitory into a UserController:

```
<?php
namespace App\Controllers;

use \CroudTech\RepositoriesTests\Repositories\Contracts\UserRepositoryContract;

class UserController extends Controller
{
  protected $repository;

  public function __construct(UserRepositoryContract $user_repository)
  {
    $this->repository = $user_repository;
  }
}
```
