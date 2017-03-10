<?php
namespace CroudTech\RepositoryTests\Repositories;

use CroudTech\Repositories\BaseRepository;
use CroudTech\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use CroudTech\RepositoryTests\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryContract
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
