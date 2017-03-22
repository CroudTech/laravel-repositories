<?php
namespace CroudTech\Repositories\TestApp\Repositories;

use CroudTech\Repositories\BaseRepository;
use CroudTech\Repositories\TestApp\Repositories\Contracts\UserRepositoryContract;
use CroudTech\Repositories\TestApp\Models\User;

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
