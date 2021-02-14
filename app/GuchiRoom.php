<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class GuchiRoom extends Model
{
    protected $table = 'guchi_rooms';

    protected $fillable = [
        'icon',
        'title',
        'genre',
        'user_id',
    ];


    // グチ部屋（guchi_rooms）のバリデーション
    public static $guchiRoomRules = [
        'icon' => 'nullable',
        'title' => 'required | max:50',
        'genre' => 'required',
    ];

    // グチ部屋（guchi_rooms）のバリデーションメッセージ
    public static $guchiRoomsValMessages = [
        'title.required' => 'タイトルを入力してください！',
        'title.max' => 'タイトルは50文字以内にしてください！',
        'genre.required' => 'ジャンルを選択してください！',
    ];



    // ユーザー(users)とのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // グチ部屋のブックマークとのリレーション
    public function guchiBookmarks()
    {
        return $this->hasMany('App\GuchiBookmark');
    }

    // グチとのリレーション
    public function guchis()
    {
        return $this->hasMany('App\Guchi');
    }

    // グチへのいいねとのリレーション
    public function guchiGoods()
    {
        return $this->hasManyThrough('App\GuchiGood', 'App\Guchi');
    }
}
