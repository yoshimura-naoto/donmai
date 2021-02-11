<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'replies';

    protected $fillable = [
        'user_id',
        'comment_id',
        'body',
    ];


    // バリデーションルール
    public static $replyRules = [
        'body' => 'required',
    ];

    // バリデーションメッセージ
    public static $replyValMessages = [
        'body.required' => '返信を入力してください！',
    ];


    // コメント（comments）とのリレーション
    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }

    // ユーザー（users）とのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // コメントへの返信のいいね（reply_goods）とのリレーション
    public function replyGoods()
    {
        return $this->hasMany('App\ReplyGood');
    }
}
