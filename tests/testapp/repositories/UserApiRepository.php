<?php
namespace CroudTech\RepositoriesTests\Repositories;

use CroudTech\Repositories\BaseRepository;
use CroudTech\RepositoriesTests\Repositories\Contracts\UserRepositoryContract;
use CroudTech\RepositoriesTests\Models\User;

class UserApiRepository extends BaseRepository implements UserRepositoryContract
{
    /**
     * Return the model name for this repository
     *
     * @method getModelName
     * @return string
     */
    public function getModelName() : string
    {
        return User::class;
    }
}
