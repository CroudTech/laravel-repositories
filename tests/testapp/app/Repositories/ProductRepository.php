<?php
namespace CroudTech\Repositories\TestApp\Repositories;

use CroudTech\Repositories\BaseRepository;
use CroudTech\Repositories\TestApp\Repositories\Contracts\ProductRepositoryContract;
use CroudTech\Repositories\TestApp\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryContract
{
    /**
     * Return the model name for this repository
     *
     * @method getModelName
     * @return string
     */
    public function getModelName() : string
    {
        return Product::class;
    }
}
