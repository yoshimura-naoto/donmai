<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'post_id',
        'body',
    ];


    // バリデーション
    public static $commentRules = [
        'body' => 'required',
    ];

    // バリデーションメッセージ
    public static $commentValMessages = [
        'body.required' => 'コメントを入力してください！',
    ];


    // 投稿（posts）とのリレーション
    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    // ユーザー（ユーザー）とのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // コメントのいいね（comment_goods）とのリレーション
    public function commentGoods()
    {
        return $this->hasMany('App\CommentGood');
    }

    // コメントへの返信（replies）とのリレーション
    public function replies()
    {
        return $this->hasMany('App\Reply');
    }
}
