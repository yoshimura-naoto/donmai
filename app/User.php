<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'icon',
        'pr',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // ユーザー登録のバリデーション
    public static $registerRules = [
        'name' => 'required | max:30',
        'email' => 'required | email | unique:users,email',
        'password' => 'required | min:6 | regex:/^[a-zA-Z0-9-_]+$/ | confirmed',
    ];

    // ユーザー登録のバリデーションメッセージ
    public static $registerValMessages = [
        'name.required' => '名前を入力してください！',
        'name.max' => '名前は30文字以内で入力してください！',
        'email.required' => 'メールアドレスを入力してください！',
        'email.email' => '正しくメールアドレスを入力してください！',
        'email.unique' => 'そのメールアドレスはすでに使われています！',
        'password.required' => 'パスワードを入力してください！',
        'password.min' => 'パスワードは６文字以上にしてください！',
        'password.confirmed' => 'もう一度パスワードを確認してください！',
        'password.regex' => 'パスワードは半角英数字、ハイフン、アンダーバーのみ可です！',
    ];


    // ログインのバリデーションルール
    public static $loginRules = [
        'email' => 'required | email',
        'password' => 'required',
    ];

    // ログインのバリデーションメッセージ
    public static $loginValMessages = [
        'email.required' => 'メールアドレスを入力してください！',
        'email.email' => '正しくメールアドレスを入力してください！',
        'password.required' => 'パスワードを入力してください！',
    ];


    // ユーザーのプロフィール更新のバリデーション(update)
    public static $updateRules = [
        'name' => 'required | max:30',
        'pr' => 'max:100 | nullable',
        'icon' => 'nullable',
        'iconImage' => 'nullable',
    ];

    // ユーザーのプロフィール更新のバリデーションメッセージ
    public static $updateValMessages = [
        'name.required' => '名前を入力してください！',
        'name.max' => '名前は30文字以内で入力してください！',
        'pr.max' => '自己紹介は100文字以内にしてください！',
    ];


    // パスワード更新のバリデーション
    public static $passwordRules = [
        'currentPassword' => 'required',
        'newPassword' => 'required | min:6 | regex:/^[a-zA-Z0-9-_]+$/ | confirmed'
    ];

    public static $passwordValMessages = [
        'currentPassword.required' => '現在のパスワードを入力してください！',
        'newPassword.required' => '新しいパスワードを入力してください！',
        'newPassword.min' => 'パスワードは6文字以上にしてください！',
        'newPassword.confirmed' => 'もう一度確認パスワードを入力してください！',
        'newPassword.regex' => 'パスワードは半角英数字、ハイフン、アンダーバーのみ可です！',
    ];



    // 投稿（posts）とのリレーション
    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    // 投稿の画像とのリレーション
    public function postImages()
    {
        return $this->hasManyThrough('App\PostImage', 'App\Post');
    }

    // どんまい（donmai）とのリレーション
    public function donmais()
    {
        return $this->hasMany('App\Donmai');
    }

    // フォロー（follows）とのリレーション（フォロー中のユーザーたち）
    public function follows()
    {
        return $this->hasMany('App\Follow');
    }

    // フォロワー（follows）とのリレーション（フォロワーたち）
    public function followers()
    {
        return $this->hasMany('App\Follow', 'following_user_id');
    }

    // コメント(comments)とのリレーション
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    // コメントへのいいね（comment_goods）とのリレーション
    public function commentGoods()
    {
        return $this->hasMany('App\CommentGood');
    }

    // コメントへの返信（replies）とのリレーション
    public function replies()
    {
        return $this->hasMany('App\Reply');
    }

    // コメントへの返信へのいいね（reply_goods）とのリレーション
    public function replyGoods()
    {
        return $this->hasMany('App\ReplyGood');
    }

    // グチ部屋(guchi_rooms)とのリレーション
    public function guchiRooms()
    {
        return $this->hasMany('App\GuchiRoom');
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
        return $this->hasMany('App\GuchiGood');
    }
}
