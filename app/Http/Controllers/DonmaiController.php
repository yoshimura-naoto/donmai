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
    public function getDonmaiUser($id, Request $request)
    {
        // どんまいが０件だった場合は空のデータを返す
        if (Donmai::where('post_id', $id)->count() === 0) {
            $data = [
                'donmais' => [],
                'lastDonamiId' => null,
            ];

            return $data;
        }

        $loadedLastDonmaiId = $request->last_donmai_id;  // フロントで取得された最後のどんまいのID

        // まだフロントで全くコメントを取得していない場合
        if ($loadedLastDonmaiId === 'nothing') {
            $loadedLastDonmaiId = Donmai::where('post_id', $id)->select('id')->latest()->first()->id + 1;
        }

        // 新着順で８件ずつコメント取得
        $donmais = Donmai::where('post_id', $id)
                        ->where('id', '<', $loadedLastDonmaiId)
                        ->with(['user' => function ($query) {
                            $query->withCount(['followers' => function (Builder $query) {
                                $query->where('user_id', Auth::id());
                            }]);
                        }])
                        ->orderBy('id', 'desc')
                        ->limit(8)
                        ->get();

        // それぞれのユーザーを認証ユーザーがフォローしているか判定
        foreach ($donmais as $donmai) {
            if ($donmai->user->followers_count > 0) {
                $donmai->user->followed = true;
            } else {
                $donmai->user->followed = false;
            }
        }

        $data = [
            'donmais' => $donmais,
            'lastDonmaiId' => Donmai::where('post_id', $id)->select('id')->first()->id,
        ];

        return $data;
    }
}
