<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendCharacter extends Model
{
    // テーブル名
    protected $table = 'friend_characters';

    // デフォルトのタイムスタンプ管理を無効にする（必要に応じて）
    public $timestamps = false;

    // その他のモデル設定やリレーションなど
}
