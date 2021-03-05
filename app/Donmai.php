<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Donmai extends Model
{
    protected $table = 'donmais';

    protected $fillable = [
        'user_id',
        'post_id',
    ];


    // ユーザー（users）とのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // 投稿（posts）とのリレーション
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
