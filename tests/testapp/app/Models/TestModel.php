<?php
namespace CroudTech\Repositories\TestApp\Models;

use \Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
