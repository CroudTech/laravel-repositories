<?php
namespace CroudTech\Repositories\TestApp\Transformers;

use \CroudTech\Repositories\Contracts\TransformerContract;
use CroudTech\Repositories\Contracts\RequestTransformerContract;
use \Illuminate\Database\Eloquent\Model;
use \League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract implements TransformerContract, RequestTransformerContract
{
    public function transform(Model $product)
    {
        return [
            'product_name' => $product->name,
            'product_description' => $product->description,
            'product_price' => $product->price,
        ];
    }

    /**
     * Transform data on the way in
     *
     * @method transformIn
     * @return {[type]     [description]
     */
    public function request(array $data) : array
    {
        $collection = collect($data);
        $modified_keys = $collection->keys()->map(function ($key) {
            switch ($key) {
                default:
                    return $key;
                break;
                case 'product_name':
                    return 'name';
                break;
                case 'product_description':
                    return 'description';
                break;
            }
        });
        
        return $modified_keys->combine($collection)->toArray();
    }
}
