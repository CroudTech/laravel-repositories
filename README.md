# laravel-repositories

[![Build Status](https://travis-ci.org/jscrobinson/laravel-repositories.svg?branch=master)](https://travis-ci.org/jscrobinson/laravel-repositories)

## Use Repositories with the Laravel framework

### Setup

This package uses dependency injection via the Laravel container to inject repositories into controllers or other classes that may require them.

Add the repository definitions into your app config file as follows:

```
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
```

To specify the repository required by a controller just add it's contract or classname into the construct method of the controller.

For example to inject the UserRespsitory into a UserController:

```
<?php
namespace App\Controllers;

use \CroudTech\RepositoryTests\Repositories\Contracts\UserRepositoryContract;

class UserController extends Controller
{
  protected $repository;
  
  public function __construct(UserRepositoryContract $user_repository)
  {
    $this->repository = $user_repository;
  }
}
```

