<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\User;
use App\Post;
use App\Donmai;

class DonmaiController extends Controller
{
    // 投稿へのどんまい
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


    // その投稿にどんまいしたユーザーの取得
    public function getDonmaiUser($id)
    {
        $users = User::select(['id', 'icon', 'name'])
                    ->whereHas('donmais', function (Builder $query) use ($id) {
                        $query->where('post_id', $id);
                    })
                    ->withCount(['followers' => function (Builder $query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->paginate(8);

        // それぞれのユーザーを認証ユーザーがフォローしているか判定
        foreach ($users as $user) {
            if ($user->followers_count > 0) {
                $user->followed = true;
            } else {
                $user->followed = false;
            }
        }

        return $users;
    }
}
