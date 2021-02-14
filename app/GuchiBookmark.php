<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuchiBookmark extends Model
{
    protected $table = 'guchi_bookmarks';

    protected $fillable = [
        'user_id',
        'guchi_room_id',
    ];


    // グチ部屋とのリレーション
    public function guchiRoom()
    {
        return $this->belongsTo('App\GuchiRoom');
    }

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
