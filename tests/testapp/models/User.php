<?php
namespace Croud\RepositoryTests\Models;

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
