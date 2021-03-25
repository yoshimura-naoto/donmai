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
    // 投稿へのどんまい（ツイッターなどでいういいね機能）
    public function donmai($id)
    {
        Donmai::create([
            'user_id' => Auth::id(),
            'post_id' => intval($id),
        ]);
    }


    // どんまいのキャンセル処理
    public function unDonmai($id)
    {
        Donmai::where('user_id', Auth::id())
                ->where('post_id', intval($id))
                ->delete();
    }


    // その投稿にどんまいしたユーザーたちを、最近どんまいした人順で取得
    public function getDonmaiUser($id)
    {
        $donmais = Donmai::where('post_id', $id)
                        ->with(['user' => function ($query) {
                            $query->withCount(['followers' => function (Builder $query) {
                                $query->where('user_id', Auth::id());
                            }]);
                        }])
                        ->orderBy('id', 'desc')
                        ->paginate(8);

        // それぞれのユーザーを認証ユーザーがフォローしているか判定
        foreach ($donmais as $donmai) {
            if ($donmai->user->followers_count > 0) {
                $donmai->user->followed = true;
            } else {
                $donmai->user->followed = false;
            }
        }

        return $donmais;
    }
}
