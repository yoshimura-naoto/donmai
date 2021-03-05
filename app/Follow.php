<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';

    protected $fillable = [
        'user_id',
        'following_user_id',
    ];


    // フォローしているユーザー
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // フォローされているユーザー
    public function followedUser()
    {
        return $this->belongsTo('App\User', 'following_user_id');
    }
}
