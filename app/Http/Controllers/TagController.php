<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\User;
use App\Post;
use App\PostImage;
use App\Tag;
use App\Donmai;
use App\Reply;


class TagController extends Controller
{
    // タグのトレンドランキング（ここ１週間以内で紐つけられた投稿が多い順）
    public function getTrend()
    {
        $lastWeek = new Carbon('-30 day', 'Asia/Tokyo');

        $tags = Tag::withCount(['posts' => function (Builder $query) use ($lastWeek) {
                        $query->where('posts.created_at', '>', $lastWeek);
                    }])
                    ->orderBy('posts_count', 'desc')
                    ->limit(10)
                    ->get();

        // フロントエンド用データの初期化
        foreach ($tags as $tag) {
            $tag->postCountShow = 0;
            $tag->postCountNow = 0;
        }

        return $tags;
    }



    // そのタグを関連づけた投稿の数を取得
    public function tagCount($name)
    {
        $tag = Tag::where('name', $name)
                    ->withCount(['posts'])
                    ->first();

        $tagPostsCount = null;

        if ($tag) {
            $tagPostsCount = $tag->posts_count;
        } else {
            $tagPostsCount = 0;
        }

        return $tagPostsCount;
    }



    // そのタグを関連づけた投稿を新着順で取得（３件ずつ無限スクロール）
    public function getTagsNewPosts($name, Request $request)
    {
        $posts = Post::whereHas('tags', function (Builder $query) use ($name) {
                        $query->where('name', $name);
                    })
                    ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->withCount('donmais', 'comments', 'replies')
                    ->orderBy('id', 'desc')
                    ->offset($request->loaded_posts_count)
                    ->limit(3)
                    ->get();

        foreach ($posts as $post) {
            $post->genre = Post::$genres[$post->genre_index];
            if (count($post->donmais) > 0) {
                $post->donmai = true;
            } else {
                $post->donmai = false;
            }
            $post->donmaiCount = $post->donmais_count;
            $post->commentCount = $post->comments_count + $post->replies_count;
            $post->postMenuOpened = false;
        }

        $data = [
            'posts' => $posts,
            'postsTotal' => Post::whereHas('tags', function (Builder $query) use ($name) {
                                    $query->where('name', $name);
                                })
                                ->count(),
        ];

        return $data;
    }


    // タグページで投稿のみをどんまい数が多い順で取得
    public function getTagsPopularPosts($name, Request $request)
    {
        // 投稿一覧の取得
        $posts = Post::whereHas('tags', function (Builder $query) use ($name) {
                        $query->where('name', $name);
                    })
                    ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->withCount('donmais', 'comments', 'replies')
                    ->orderBy('donmais_count', 'desc')
                    ->orderBy('comments_count', 'desc')
                    ->orderBy('id', 'desc')
                    ->offset($request->loaded_posts_count)
                    ->limit(3)
                    ->get();

        foreach ($posts as $post) {
            $post->genre = Post::$genres[$post->genre_index];
            if (count($post->donmais) > 0) {
                $post->donmai = true;
            } else {
                $post->donmai = false;
            }
            $post->donmaiCount = $post->donmais_count;
            $post->commentCount = $post->comments_count + $post->replies_count;
            $post->postMenuOpened = false;
        }

        $data = [
            'posts' => $posts,
            'postsTotal' => Post::whereHas('tags', function (Builder $query) use ($name) {
                                $query->where('name', $name);
                            })
                            ->count(),
        ];

        return $data;
    }
}
