<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'body',
        'genre_id',
    ];

    // ジャンル
    public static $genres = [
        ['route' => 'jobs', 'name' => '仕事'],
        ['route' => 'life', 'name' => '日常'],
        ['route' => 'relationship', 'name' => '人間関係'],
        ['route' => 'dozi', 'name' => 'どじ'],
        ['route' => 'shame', 'name' => '恥かいた'],
        ['route' => 'school', 'name' => '学校'],
        ['route' => 'love', 'name' => '恋愛'],
        ['route' => 'marriage', 'name' => '結婚生活'],
        ['route' => 'game', 'name' => 'ゲーム'],
        ['route' => 'disease', 'name' => '病気'],
        ['route' => 'heart', 'name' => '心'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
        ['route' => 'unko', 'name' => 'うんこ'],
    ];

    // ジャンルのrouteからジャンルのインデックスを取得
    public static function getGenreIndex($route)
    {
        $genreRoutes = array_column($genres, 'route');
        return array_search($route, $genreRoutes);
    }


    // 投稿のバリデーション（create）
    public static $postRules = [
        'body' => 'required | max:300',
        'genreIndex' => 'required',
        'tags' => 'max:100',
    ];

    // 投稿のバリデーションメッセージ（create）
    public static $postValMessages = [
        'body.required' => '本文を入力してください！',
        'body.max' => '本文は300文字以内にしてください！',
        'genreIndex.required' => 'ジャンルを選択してください！',
        'tags.max' => 'タグ欄は100字以内にしてください！',
    ];



    // ユーザー（users）とのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // タグ（tags）とのリレーション
    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    // 投稿の画像（post_images）とのリレーション
    public function postImages()
    {
        return $this->hasMany('App\PostImage');
    }

    // どんまい（donmais）とのリレーション
    public function donmais()
    {
        return $this->hasMany('App\Donmai');
    }

    // コメント(comments)とのリレーション
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    // コメントへの返信とのリレーション
    public function replies()
    {
        return $this->hasManyThrough('App\Reply', 'App\Comment');
    }

    
}
