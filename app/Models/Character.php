<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    // 自動的にJSONとして保存・取得するフィールドを指定
    protected $casts = [
        'abilities' => 'array',
        'skills' => 'array',
    ];
}
