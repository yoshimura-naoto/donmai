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
    // コメントへの返信の投稿
    public function create(Request $request)
    {
        $request->validate(Reply::$replyRules, Reply::$replyValMessages);

        $reply = Reply::create([
            'user_id' => Auth::id(),
            'comment_id' => $request->commentId,
            'body' => $request->body,
        ]);

        $newReply = Reply::where('id', $reply->id)
                        ->with(['user', 'replyGoods'])
                        ->first();

        $newReply->goodCount = 0;
        $newReply->gooded = false;

        return response()->json($newReply, 200);

        // return response()->json([
        //     'replyId' => $reply->id,
        // ]);
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
