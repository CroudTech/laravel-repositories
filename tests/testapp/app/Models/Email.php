<?php
namespace CroudTech\Repositories\TestApp\Models;

use \Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
    ];
}
