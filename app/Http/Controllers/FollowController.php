<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Follow;

class FollowController extends Controller
{
    // フォロー中の人たちのデータを返す
    public function followingShow($id)
    {
        // 自分のid
        $authId = Auth::id();

        // あるユーザーがフォローしてる人々と自分がフォローしてる人々
        $follows = Follow::where('user_id', $id)
                        ->orWhere('user_id', $authId)
                        ->get();

        $followingIds = [];     // あるユーザーがフォローしている人々のidの配列
        $myFollowingIds = [];   // 自分がフォローしてる人々のidの配列
        
        for ($i = 0; $i < count($follows); $i++) {
            if ($follows[$i]->user_id == $id) {
                array_push($followingIds, $follows[$i]->following_user_id);
                if ($authId == $id) {
                    array_push($myFollowingIds, $follows[$i]->following_user_id);
                }
            } else {
                array_push($myFollowingIds, $follows[$i]->following_user_id);
            }
        }

        // あるユーザーがフォローしているユーザーを取得
        $users = User::whereIn('id', $followingIds)->get();

        // あるユーザーがフォローしているユーザーを自分がフォローしているか判定
        for ($i = 0; $i < count($followingIds); $i++) {
            if (in_array($followingIds[$i], $myFollowingIds, true)) {
                $users[$i]->followed = true;
            } else {
                $users[$i]->followed = false;
            }
        }

        return $users;
    }


    // フォロワーたちを返す
    public function followerShow($id)
    {
        // 自分のIDを取得
        $authId = Auth::id();

        // あるユーザーのフォロワーと自分がフォローしてる人たち
        $follows = Follow::where('following_user_id', $id)
                    ->orWhere('user_id', $authId)
                    ->get();

        $followerIds = [];      // あるユーザーのフォロワーたちのidの配列
        $myFollowingIds = [];   // 自分がフォローしてる人たちのidの配列

        for ($i = 0; $i < count($follows); $i++) {
            // あるユーザーのフォロワーたちのidの配列を取得（自分は除く）
            if ($follows[$i]->following_user_id == $id) {
                array_push($followerIds, $follows[$i]->user_id);
            // 自分がフォローしてる全ユーザーたちのidの配列を取得
            } else if ($follows[$i]->user_id === $authId) {
                array_push($myFollowingIds, $follows[$i]->following_user_id);
            }
        }

        // あるユーザーのフォロワーを取得
        $followers = User::whereIn('id', $followerIds)->get();
        
        // あるユーザーのフォロワーを自分がフォローしているかどうかの判定
        for ($i = 0; $i < count($followerIds); $i++) {
            if (in_array($followerIds[$i], $myFollowingIds, true)) {
                $followers[$i]->followed = true;
            } else {
                $followers[$i]->followed = false;
            }
        }

        return $followers;
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
