<?php

namespace App\Model\Passport;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'passport';
    protected $table = 'user';
}
