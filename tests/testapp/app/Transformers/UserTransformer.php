<?php
namespace CroudTech\Repositories\TestApp\Transformers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use \Illuminate\Database\Eloquent\Model;
use \League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract implements TransformerContract
{
    protected $availableIncludes = [
        'address',
    ];

    public function transform(Model $user)
    {
        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];
    }

    /**
     * [includeAddress description]
     * @method includeAddress
     * @return {[type]        [description]
     */
    public function includeAddress(Model $user)
    {
        return $this->item($user->address, app()->make(AddressTransformer::class));
    }
}
