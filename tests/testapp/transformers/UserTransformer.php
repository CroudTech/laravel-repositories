<?php
namespace CroudTech\RepositoriesTests\Transformers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use Illuminate\Database\Eloquent\Model;

class UserTransformer implements TransformerContract
{
    public function transform(Model $user)
    {
        return $user->toArray();
    }
}
