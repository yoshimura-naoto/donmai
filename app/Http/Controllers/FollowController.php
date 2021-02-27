<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\User;
use App\Follow;

class FollowController extends Controller
{
    // あるユーザーのフォロー中の人たちを返す（８件ずつ無限スクロール）
    public function followingShow($id)
    {
        // そのユーザーがフォローしてる人々と自分（認証ユーザー）がフォローしてる人々
        $users = User::whereHas('followers', function (Builder $query) use ($id) {
                        $query->where('user_id', $id);
                    })
                    ->with(['followers' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->paginate(8);

        // 認証ユーザーが、そのユーザーがフォローしているユーザーをフォローしているかどうか
        foreach ($users as $user) {
            if (count($user->followers) > 0) {
                $user->followed = true;
            } else {
                $user->followed = false;
            }
        }

        return $users;
    }



    // あるユーザーのフォロワーたちを返す（８件ずつ無限スクロール）
    public function followerShow($id)
    {
        // あるユーザーのフォロワーと自分（認証ユーザー）がフォローしてる人たち
        $users = User::whereHas('follows', function (Builder $query) use ($id) {
                        $query->where('following_user_id', $id);
                    })
                    ->with(['followers' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->paginate(8);
        
        // 認証ユーザーがそのユーザーのフォロワーをフォローしているか
        foreach ($users as $user) {
            if (count($user->followers) > 0) {
                $user->followed = true;
            } else {
                $user->followed = false;
            }
        }

        return $users;
    }


    // フォロー処理
    public function follow(Request $request)
    {
        Follow::create([
            'user_id' => Auth::id(),
            'following_user_id' => $request->id,
        ]);
    }


    // アンフォロー処理
    public function unfollow(Request $request)
    {
        Follow::where('user_id', Auth::id())
                ->where('following_user_id', $request->id)
                ->delete();
    }
}
