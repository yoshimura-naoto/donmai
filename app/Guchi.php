<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guchi extends Model
{
    protected $table = 'guchis';

    protected $fillable = [
        'body',
        'user_id',
        'guchi_room_id',
        'anonymous',
    ];


    // グチ投稿のバリデーション
    public static $guchiRules = [
        'body' => 'required | max:250',
    ];

    // グチ投稿のバリデーションメッセージ
    public static $guchiValMessages = [
        'body.required' => '入力されていません！',
        'body.max' => '250文字以内にしてください！',
    ];


    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // グチ部屋とのリレーション
    public function guchiRoom()
    {
        return $this->belongsTo('App\GuchiRoom');
    }

    // グチの画像とのリレーション
    public function guchiImages()
    {
        return $this->hasMany('App\GuchiImage');
    }

    // グチへのいいねとのリレーション
    public function guchiGoods()
    {
        return $this->hasMany('App\GuchiGood');
    }
}
