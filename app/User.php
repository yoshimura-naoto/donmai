<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
        'name' => 'required',
        'email' => 'required | email | unique:users,email',
        'password' => 'required | min:6 | confirmed',
    ];

    // ユーザー登録のバリデーションメッセージ
    public static $registerValMessages = [
        'name.required' => '名前を入力してください！',
        'email.required' => 'メールアドレスを入力してください！',
        'email.email' => '正しくメールアドレスを入力してください！',
        'email.unique' => 'そのメールアドレスはすでに使われています！',
        'password.required' => 'パスワードを入力してください！',
        'password.min' => 'パスワードは６文字以上にしてください！',
        'password.confirmed' => 'パスワードの確認に失敗しました！',
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
}
