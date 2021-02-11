<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Post;
use App\Donmai;

class DonmaiController extends Controller
{
    // どんまい処理
    public function donmai($id)
    {
        Donmai::create([
            'user_id' => Auth::id(),
            'post_id' => intval($id),
        ]);
    }


    // どんまいキャンセル処理
    public function unDonmai($id)
    {
        Donmai::where('user_id', Auth::id())
                ->where('post_id', intval($id))
                ->delete();
    }


    // どんまいしたユーザーの取得
    public function getDonmaiUser($id)
    {
        $donmais = Donmai::where('post_id', $id)
                        ->with('user.followers')
                        ->get();

        $users = $donmais->pluck('user');

        // それぞれのユーザーを認証ユーザーがフォローしているか判定
        foreach ($users as $user) {
            if ($user->followers->contains('user_id', Auth::id())) {
                $user->followed = true;
            } else {
                $user->followed = false;
            }
        }

        return $users;
    }
}
