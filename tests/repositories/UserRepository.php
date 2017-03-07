<?php
namespace Croud\RepositoryTests\Repositories;

use Croud\Repositories\BaseRepository;
use Croud\RepositoryTests\Repositories\Contracts\UserRepositoryContract;
use Croud\RepositoryTests\Models\User;

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
