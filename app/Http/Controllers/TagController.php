<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Post;
use App\PostImage;
use App\Tag;
use App\Donmai;
use App\Reply;


class TagController extends Controller
{
    // タグのトレンドランキング
    public function getTrend()
    {
        $tags = Tag::withCount('posts')
                    ->orderBy('posts_count', 'desc')
                    ->get();

        foreach ($tags as $tag) {
            $tag->postCountShow = 0;
            $tag->postCountNow = 0;
        }

        // $tags[0]->posts_count = 15;
        // $tags[1]->posts_count = 13;
        // $tags[2]->posts_count = 11;
        // $tags[3]->posts_count = 9;
        // $tags[4]->posts_count = 7;
        // $tags[5]->posts_count = 5;
        // $tags[6]->posts_count = 4;
        // $tags[7]->posts_count = 2;

        return $tags;
    }



    // そのタグを関連づけた投稿の数
    public function tagCount($name)
    {
        $tag = Tag::where('name', $name)
                    ->withCount('posts')
                    ->first();

        $tagPostsCount = null;

        if ($tag) {
            $tagPostsCount = $tag->posts_count;
        } else {
            $tagPostsCount = 0;
        }

        return $tagPostsCount;
    }



    // タグページの投稿を新着順で取得
    public function tagsNewGet($name)
    {
        // 認証ユーザーの取得
        $authUser = Auth::user();

        // 投稿一覧の取得
        $posts = Post::whereHas('tags', function (Builder $query) use ($name) {
                    $query->where('name', $name);
                })
                ->with(['user', 'tags', 'postImages', 'donmais' => function ($query) {
                    $query->where('user_id', Auth::id());
                }])
                ->withCount('donmais', 'comments', 'replies')
                ->orderBy('created_at', 'desc')
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
        }

        $data = [
            'authUser' => $authUser,
            'posts' => $posts,
        ];

        return $data;
    }


    // タグページの投稿をどんまい数が多い順で取得
    public function tagsPopularGet($name)
    {
        // 認証ユーザーの取得
        $authUser = Auth::user();

        // 投稿一覧の取得
        $posts = Post::whereHas('tags', function (Builder $query) use ($name) {
                    $query->where('name', $name);
                })
                ->with(['user', 'tags', 'postImages', 'donmais' => function ($query) {
                    $query->where('user_id', Auth::id());
                }])
                ->withCount('donmais', 'comments', 'replies')
                ->orderBy('donmais_count', 'desc')
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
        }

        $data = [
            'authUser' => $authUser,
            'posts' => $posts,
        ];

        return $data;
    }
}
