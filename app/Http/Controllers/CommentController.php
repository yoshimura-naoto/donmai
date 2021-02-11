<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Comment;
use App\CommentGood;
use App\User;

class CommentController extends Controller
{
    // コメントの取得
    public function getComments($id)
    {
        $comments = Comment::where('post_id', $id)
                            ->with(['user', 'commentGoods', 'replies.user', 'replies.replyGoods'])
                            ->orderBy('created_at', 'desc')
                            ->get();

        foreach ($comments as $comment) {
            $comment->goodCount = count($comment->commentGoods);
            if ($comment->commentGoods->contains('user_id', Auth::id())) {
                $comment->gooded = true;
            } else {
                $comment->gooded = false;
            }
            $comment->replyShow = false;
            $comment->replyCount = count($comment->replies);
            $comment->replyFormOpened = false;
            $comment->replyInput = '';
            $comment->replyErrors = [];
            foreach ($comment->replies as $reply) {
                $reply->goodCount = count($reply->replyGoods);
                if ($reply->replyGoods->contains('user_id', Auth::id())) {
                    $reply->gooded = true;
                } else {
                    $reply->gooded = false;
                }
            }
        }

        return $comments;
    }



    // 投稿に対するコメントの投稿
    public function create(Request $request)
    {
        $request->validate(Comment::$commentRules, Comment::$commentValMessages);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $request->postId,
            'body' => $request->body,
        ]);

        $newComment = Comment::where('id', $comment->id)
                            ->with(['user', 'commentGoods', 'replies.user', 'replies.replyGoods'])
                            ->first();

        $newComment->goodCount = 0;
        $newComment->gooded = false;
        $newComment->replyShow = false;
        $newComment->replyCount = 0;
        $newComment->replyFormOpened = false;
        $newComment->replyInput = '';
        $newComment->replies = [];
        $newComment->replyErrors = [];
        foreach ($newComment->replies as $reply) {
            $reply->goodCount = count($reply->replyGoods);
            if ($reply->replyGoods->contains('user_id', Auth::id())) {
                $reply->gooded = true;
            } else {
                $reply->gooded = false;
            }
        }

        return response()->json($newComment, 200);
        // return response()->json([
        //     'commentId' => $comment->id,
        // ]);
    }



    // コメントへのいいね処理
    public function good($id)
    {
        CommentGood::create([
            'user_id' => Auth::id(),
            'comment_id' => $id,
        ]);
    }


    // コメントへのいいねの削除
    public function ungood($id)
    {
        CommentGood::where('user_id', Auth::id())
                    ->where('comment_id', $id)
                    ->delete();
    }
}
