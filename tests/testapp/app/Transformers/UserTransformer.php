<?php
namespace CroudTech\Repositories\TestApp\Transformers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use \Illuminate\Database\Eloquent\Model;
use \League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract implements TransformerContract
{
    public function transform(Model $user)
    {
        return $user->toArray();
    }
}
