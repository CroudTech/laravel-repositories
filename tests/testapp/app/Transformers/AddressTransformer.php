<?php
namespace CroudTech\Repositories\TestApp\Transformers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use \Illuminate\Database\Eloquent\Model;
use \League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract implements TransformerContract
{
    public function transform(Model $address)
    {
        return [
            'address_line_1' => $address->address_line_1,
        ];
    }
}
