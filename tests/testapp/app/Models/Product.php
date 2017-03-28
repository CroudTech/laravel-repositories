<?php
namespace CroudTech\Repositories\TestApp\Models;

use \Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];
}
