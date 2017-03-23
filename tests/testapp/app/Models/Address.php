<?php
namespace CroudTech\Repositories\TestApp\Models;

use \Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'address_line_1',
    ];
}
