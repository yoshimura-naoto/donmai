<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Reply;
use App\ReplyGood;
use App\User;
use App\Comment;

class ReplyController extends Controller
{
    // コメントへの返信の取得（５件ずつ）
    public function get($id, Request $request)
    {
        $replies = Reply::where('comment_id', $id)
                        ->with(['user:id,name,icon', 'replyGoods' => function ($query) {
                            $query->where('user_id', Auth::id());
                        }])
                        ->withCount('replyGoods')
                        ->orderBy('id', 'asc')
                        ->offset($request->loaded_replies_count)
                        ->limit(5)
                        ->get();

        foreach ($replies as $reply) {
            $reply->goodCount = $reply->reply_goods_count;
            // 認証ユーザーがそれぞれの返信にいいねしているかどうか
            if (count($reply->replyGoods) > 0) {
                $reply->gooded = true;
            } else {
                $reply->gooded = false;
            }
        }

        $data = [
            'replies' => $replies,
            'repliesTotal' => Reply::where('comment_id', $id)->count(),
        ];

        return $data;
    }


    // コメントへの返信の投稿
    public function create(Request $request)
    {
        // バリデーション
        $request->validate(Reply::$replyRules, Reply::$replyValMessages);

        // 返信の作成
        $reply = Reply::create([
            'user_id' => Auth::id(),
            'comment_id' => $request->commentId,
            'body' => $request->body,
        ]);

        // 投稿した返信をレスポンスとして返す
        $newReply = Reply::where('id', $reply->id)
                        ->with(['user:id,name,icon', 'replyGoods'])
                        ->first();

        $newReply->goodCount = 0;
        $newReply->gooded = false;

        return response()->json($newReply, 200);
    }



    // コメントへの返信の削除
    public function delete($id)
    {
        Reply::where('id', $id)->delete();
    }



    // コメントへの返信へのいいね
    public function good($id)
    {
        ReplyGood::create([
            'user_id' => Auth::id(),
            'reply_id' => $id,
        ]);
    }



    // コメントへの返信へのいいねのキャンセル
    public function ungood($id)
    {
        ReplyGood::where('user_id', Auth::id())
                    ->where('reply_id', $id)
                    ->delete();
    }
}
