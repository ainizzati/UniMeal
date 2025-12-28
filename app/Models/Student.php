<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $primaryKey = 'matric_no';
    public $incrementing = false;
    protected $keyType = 'string';

    //before (missing password protection)
    protected $fillable = ['matric_no', 'name', 'email', 'password'];

    //after (added hides password & auto hashing with Laravel 11)
    protected $hidden = [
    'password',
    'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
}
