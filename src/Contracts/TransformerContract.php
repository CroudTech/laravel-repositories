<?php
namespace CroudTech\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface TransformerContract
{
    public function transform(Model $model);
}
