<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use InterventionImage;

use App\User;
use App\Follow;
use Storage;


class UserController extends Controller
{
    // ユーザー表示
    public function show($id)
    {
        $authId = Auth::id();
        $user = User::where('id', $id)->first();

        // 認証ユーザーがそのユーザーをフォローしているかどうかチェック
        $followedByMe = Follow::where('user_id', $authId)
                ->where('following_user_id', $user->id)
                ->exists();
        if (!$followedByMe) {
            $user->followed = false;
        } else {
            $user->followed = true;
        }

        // フォロー数とフォロワー数を取得
        $followingCount = Follow::where('user_id', $user->id)->count();
        $followerCount = Follow::where('following_user_id', $user->id)->count();
        $user->follow = $followingCount;
        $user->follower = $followerCount;

        return $user;
    }


    // ユーザーのプロフィール更新
    public function update(Request $request)
    {
        $request->validate(User::$updateRules, User::$updateValMessages);

        $user = User::where('id', $request->id)->first();

        // アイコン画像アップロードの処理
        // $imageExistense = $request->hasFile('iconImage');

        var_dump($request->hasFile('iconImage'));
        var_dump($request->icon);
        var_dump($request->iconImage);
        
        if ($request->hasFile('iconImage')) {
            // 画像を正方形にリサイズして保存
            $image = $request->file('iconImage');
            InterventionImage::make($image)
                ->fit(200, 200)
                ->save($image);
            $path = Storage::disk('s3')->putFile('user_icon', $image, 'public');
            // 現在のアイコン画像の削除して新たに画像を保存
            Storage::disk('s3')->delete(parse_url($user->icon, PHP_URL_PATH));
            $user->icon = Storage::disk('s3')->url($path);
        } else if (!$request->icon) {
        // } else if ($request->icon == 'null') {
            Storage::disk('s3')->delete(parse_url($user->icon, PHP_URL_PATH));
            $user->icon = null;
        } else {
            $user->icon = $user->icon;
        }

        // ユーザー名と自己紹介文の更新
        $user->name = $request->name;
        $user->pr = $request->pr;

        $user->save();
    }


    // ユーザーのパスワードの更新
    public function password(Request $request)
    {
        $request->validate(User::$passwordRules, User::$passwordValMessages);

        $user = User::find($request->id);

        print_r($user->password);
        print_r($request->currentPassword);

        if (Hash::check($request->currentPassword, $user->password)) {
            $user->password = Hash::make($request->newPassword);
        } else {
            throw ValidationException::withMessages([
                'msg' => '現在のパスワードが間違っています！',
            ]);
        }

        $user->save();
    }
}
