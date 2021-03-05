<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\User;
use App\Follow;

class FollowController extends Controller
{

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



    // あるユーザーのフォロー中の人たちを、そのユーザーが最近フォローした順で返す（８件ずつ無限スクロール）
    public function followingShow($id)
    {
        // そのユーザーがフォローしてる人々と、彼らがそれぞれ自分（認証ユーザー）をフォロワーに持つかどうかを取得
        $follows = Follow::where('user_id', $id)
                        ->with(['followedUser' => function ($query) {
                            $query->with(['followers' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }]);
                        }])
                        ->orderBy('id', 'desc')
                        ->paginate(8);

        // 認証ユーザーが、そのユーザーがフォローしているユーザーをフォローしているかどうか
        foreach ($follows as $follow) {
            if (count($follow->followedUser->followers) > 0) {
                $follow->followedUser->followed = true;
            } else {
                $follow->followedUser->followed = false;
            }
        }

        return $follows;
    }



    // あるユーザーのフォロワーたちを、そのユーザーを最近フォローした人順で返す（８件ずつ無限スクロール）
    public function followerShow($id)
    {
        // あるユーザーのフォロワーと、彼らがそれぞれ自分（認証ユーザー）をフォロワーに持つかどうかを取得
        $follows = Follow::where('following_user_id', $id)
                        ->with(['user' => function ($query) {
                            $query->with(['followers' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }]);
                        }])
                        ->orderBy('id', 'desc')
                        ->paginate(8);
        
        // 認証ユーザーがそのユーザーのフォロワーをフォローしているか
        foreach ($follows as $follow) {
            if (count($follow->user->followers) > 0) {
                $follow->user->followed = true;
            } else {
                $follow->user->followed = false;
            }
        }

        return $follows;
    }
}
