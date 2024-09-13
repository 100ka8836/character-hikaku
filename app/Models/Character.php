<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = ['name', 'occupation', 'age', 'sex', 'skills', 'user_id', 'abilities'];

    // app/Models/Character.php
    protected $casts = [
        'abilities' => 'array',
        'skills' => 'array',
    ];
}