<?php
namespace CroudTech\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RequestTransformerContract
{
    /**
     * Transform the request data
     *
     * @method request
     * @param  array  $data The data to transform
     * @return array The transformed data
     */
    public function request(array $data) : array;
}
