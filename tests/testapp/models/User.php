<?php
namespace CroudTech\RepositoriesTests\Models;

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
