<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Comment;
use App\CommentGood;
use App\User;

class CommentController extends Controller
{
    // 投稿のコメントを取得（５件ずつ無限スクロール）
    public function getComments($id, Request $request)
    {
        $comments = Comment::where('post_id', $id)
                            ->with(['user', 'commentGoods' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount('commentGoods', 'replies')
                            ->orderBy('id', 'desc')
                            ->offset($request->loaded_comments_count)
                            ->limit(5)
                            ->get();

        foreach ($comments as $comment) {
            $comment->goodCount = $comment->comment_goods_count;
            // 認証ユーザーがコメントにいいねしているかどうか
            if (count($comment->commentGoods) > 0) {
                $comment->gooded = true;
            } else {
                $comment->gooded = false;
            }
            $comment->replyCount = $comment->replies_count;
            // フロントエンド用データの初期化
            $comment->replies = [];
            $comment->replyShow = false;
            $comment->replyFormOpened = false;
            $comment->replyInput = '';
            $comment->replyErrors = [];
            $comment->repliesLoading = false;
            $comment->loadRepliesMore = true;
            // $comment->repliesPage = 1;
            // $comment->isRepliesLastPage = false;
        }

        $data = [
            'comments' => $comments,
            'commentsTotal' => Comment::where('post_id', $id)->count(),
        ];

        return $data;
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

        // 投稿したコメントをレスポンスとして返す
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
    }



    // コメントの削除
    public function delete($id)
    {
        Comment::where('id', $id)->delete();
    }



    // コメントへのいいね処理
    public function good($id)
    {
        CommentGood::create([
            'user_id' => Auth::id(),
            'comment_id' => $id,
        ]);
    }


    // コメントへのいいねのキャンセル
    public function ungood($id)
    {
        CommentGood::where('user_id', Auth::id())
                    ->where('comment_id', $id)
                    ->delete();
    }
}
