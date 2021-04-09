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
    public function followingShow($id, Request $request)
    {
        // 誰もフォローしていない場合は空のデータを返す
        if (Follow::where('user_id', $id)->count() === 0) {
            $data = [
                'follows' => [],
                'lastFollowId' => null,
            ];

            return $data;
        }

        $loadedLastFollowId = $request->last_follow_id;  // フロントで取得された最後のフォロー中のユーザーのID

        // まだフロントで全くフォロー中のユーザーを取得していない場合
        if ($loadedLastFollowId === 'nothing') {
            $loadedLastFollowId = Follow::where('user_id', $id)->select('id')->latest()->first()->id + 1;
        }

        // そのユーザーがフォローしてる人々と、彼らがそれぞれ自分（認証ユーザー）をフォロワーに持つかどうかを取得
        $follows = Follow::where('user_id', $id)
                        ->where('id', '<', $loadedLastFollowId)
                        ->with(['followedUser' => function ($query) {
                            $query->with(['followers' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }]);
                        }])
                        ->orderBy('id', 'desc')
                        ->limit(6)
                        ->get();

        // 認証ユーザーが、そのユーザーがフォローしているユーザーをフォローしているかどうか
        foreach ($follows as $follow) {
            if (count($follow->followedUser->followers) > 0) {
                $follow->followedUser->followed = true;
            } else {
                $follow->followedUser->followed = false;
            }
        }

        $data = [
            'follows' => $follows,
            'lastFollowId' => Follow::where('user_id', $id)->select('id')->first()->id,
        ];

        return $data;
    }



    // あるユーザーのフォロワーたちを、そのユーザーを最近フォローした人順で返す（８件ずつ無限スクロール）
    public function followerShow($id, Request $request)
    {
        // フォロワーが全くいない場合は空のデータを返す
        if (Follow::where('following_user_id', $id)->count() === 0) {
            $data = [
                'followers' => [],
                'lastFollowerId' => null,
            ];

            return $data;
        }

        $loadedLastFollowerId = $request->last_follower_id;  // フロントで取得された最後のフォロワーのID

        // まだフロントで全くフォロワーを取得していない場合
        if ($loadedLastFollowerId === 'nothing') {
            $loadedLastFollowerId = Follow::where('following_user_id', $id)->select('id')->latest()->first()->id + 1;
        }

        // あるユーザーのフォロワーと、彼らがそれぞれ自分（認証ユーザー）をフォロワーに持つかどうかを取得
        $follows = Follow::where('following_user_id', $id)
                        ->where('id', '<', $loadedLastFollowerId)
                        ->with(['user' => function ($query) {
                            $query->with(['followers' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }]);
                        }])
                        ->orderBy('id', 'desc')
                        ->limit(6)
                        ->get();
        
        // 認証ユーザーがそのユーザーのフォロワーをフォローしているか
        foreach ($follows as $follow) {
            if (count($follow->user->followers) > 0) {
                $follow->user->followed = true;
            } else {
                $follow->user->followed = false;
            }
        }

        $data = [
            'followers' => $follows,
            'lastFollowerId' => Follow::where('following_user_id', $id)->select('id')->first()->id,
        ];

        return $data;
    }
}
