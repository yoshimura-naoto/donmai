<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use InterventionImage;

use App\User;
use App\Follow;
use Storage;


class UserController extends Controller
{
    // idからユーザー情報取得
    public function show($id)
    {
        $user = User::select(['id', 'icon', 'name', 'pr'])
                    ->where('id', $id)
                    ->withCount(['posts',
                                 'follows',
                                 'followers',
                                 'followers as followed_by_me_count' => function (Builder $query) {
                                     $query->where('user_id', Auth::id());
                                 }])
                    ->first();
        
        // 認証ユーザーがフォローしているかどうか
        if ($user->followed_by_me_count > 0) {
            $user->followed = true;
        } else {
            $user->followed = false;
        }

        return $user;
    }



    // ユーザーのプロフィール更新
    public function update(Request $request)
    {
        $request->validate(User::$updateRules, User::$updateValMessages);

        $user = User::where('id', $request->id)->first();

        // アイコン画像アップロードの処理
        if ($request->hasFile('iconImage')) {
            $image = $request->file('iconImage');
            // アイコン画像を正方形にリサイズして保存
            InterventionImage::make($image)
                ->fit(200, 200)
                ->save($image);
            $path = Storage::disk('s3')->putFile('user_icon', $image, 'public');
            // 現在のアイコン画像の削除して新たなアイコン画像のパスを取得
            Storage::disk('s3')->delete(parse_url($user->icon, PHP_URL_PATH));
            $user->icon = Storage::disk('s3')->url($path);
        } else if (!$request->icon) {
            Storage::disk('s3')->delete(parse_url($user->icon, PHP_URL_PATH));
            $user->icon = null;
        } else {
            $user->icon = $user->icon;
        }

        // ユーザー名と自己紹介文の更新
        $user->name = $request->name;
        $user->pr = $request->pr;

        $user->save();

        return response()->json([
            'userIcon' => $user->icon,
        ]);
    }


    // ユーザーのパスワードの更新
    public function password(Request $request)
    {
        $request->validate(User::$passwordRules, User::$passwordValMessages);

        $user = User::find($request->id);

        if (Hash::check($request->currentPassword, $user->password)) {
            $user->password = Hash::make($request->newPassword);
        } else {
            throw ValidationException::withMessages([
                'msg' => '現在のパスワードが間違っています！',
            ]);
        }

        $user->save();
    }



    // 検索したユーザーを取得
    public function getSearchUsers($word)
    {
        $users = User::where('name', 'like', '%' . $word . '%')
                    ->with(['followers' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->paginate(10);

        // 認証ユーザーがフォローしているかどうか
        foreach ($users as $user) {
            if (count($user->followers) > 0) {
                $user->followed = true;
            } else {
                $user->followed = false;
            }
        }

        return $users;
    }
}
