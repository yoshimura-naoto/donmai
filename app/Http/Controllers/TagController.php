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
        // $lastWeek = new Carbon('-7 day', 'Asia/Tokyo');
        $lastWeek = new Carbon('-200 day', 'Asia/Tokyo');  // 今だけ-200日に設定

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



    // そのタグが紐ついている投稿の数を取得
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
        // そのタグが紐ついている投稿の合計
        $totalPostsCount = Post::whereHas('tags', function (Builder $query) use ($name) {
                                $query->where('name', $name);
                            })
                            ->count();

        // 該当するレコードが１つもない場合
        if ($totalPostsCount === 0) {
            $data = [
                'posts' => [],
                'lastPostId' => null,
            ];
    
            return $data;
        }

        $loadedLastPostId = $request->last_post_id;

        // フロントでまだ１つも投稿が取得されていない場合
        if ($loadedLastPostId === 'nothing') {
            $loadedLastPostId = Post::whereHas('tags', function (Builder $query) use ($name) {
                                    $query->where('name', $name);
                                })
                                ->latest()->first()->id + 1;
        }

        $posts = Post::whereHas('tags', function (Builder $query) use ($name) {
                        $query->where('name', $name);
                    })
                    ->where('id', '<', $loadedLastPostId)
                    ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->withCount('donmais', 'comments', 'replies')
                    ->orderBy('id', 'desc')
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
            'lastPostId' => Post::whereHas('tags', function (Builder $query) use ($name) {
                                    $query->where('name', $name);
                                })
                                ->select('id')
                                ->orderBy('id', 'asc')
                                ->first()
                                ->id,
        ];

        return $data;
    }


    // タグページで投稿のみをどんまい数が多い順で取得
    public function getTagsPopularPosts($name, Request $request)
    {
        $loadedPostIds = array_map('intval', explode('-', $request->loaded_post_ids));  // すでに読み込まれた投稿のIDの配列

        // 投稿一覧の取得
        $posts = Post::whereNotIn('id', $loadedPostIds)
                    ->whereHas('tags', function (Builder $query) use ($name) {
                        $query->where('name', $name);
                    })
                    ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->withCount('donmais', 'comments', 'replies')
                    ->orderBy('donmais_count', 'desc')
                    ->orderBy('comments_count', 'desc')
                    ->orderBy('id', 'desc')
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
