<?php
namespace CroudTech\RepositoriesTests\Transformers;

use \CroudTech\Repositories\Contracts\TransformerContract;

class UserTransformer implements TransformerContract
{
    public function transform($user)
    {
        return $user->toArray();
    }
}
