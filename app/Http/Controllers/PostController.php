<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Post;
use App\PostImage;
use App\Tag;
use App\Donmai;
use App\Reply;
use Storage;


class PostController extends Controller
{
    // ヘッダーの初期化
    public function headInit()
    {
        $data = [
            'authUser' => Auth::user(),
            'genres' => Post::$genres,
        ];

        return $data;
    }


    // ホーム(HomeDefault）の初期化
    public function homeInit()
    {
        // 認証ユーザーの取得
        $authUser = Auth::user();

        // ジャンル一覧の取得
        $genres = Post::$genres;

        // 投稿一覧の取得
        $posts = Post::with(['user', 'tags', 'postImages', 'donmais' => function ($query) {
                    $query->where('user_id', Auth::id());
                }])
                ->withCount('donmais')
                ->withCount('comments', 'replies')
                ->orderBy('created_at', 'desc')
                ->get();

        // 投稿のジャンル、認証ユーザーがどんまいしているか、どんまい数、コメント数を取得
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
            'genres' => $genres,
            'posts' => $posts,
        ];

        return $data;
    }



    // ジャンル別ホームの初期化
    public function homeGnereInit($name)
    {
        // 認証ユーザー取得
        $authUser = Auth::user();

        // ジャンルのインデックスを取得
        $genreRoutes = array_column(Post::$genres, 'route');
        $genreIndex = array_search($name, $genreRoutes);

        // そのジャンルの投稿、投稿したユーザー、タグ、画像、を取得
        $posts = Post::where('genre_index', $genreIndex)
                ->with(['user', 'tags', 'postImages', 'donmais' => function ($query) {
                    $query->where('user_id', Auth::id());
                }])
                ->withCount('donmais')
                ->withCount('comments', 'replies')
                ->orderBy('created_at', 'desc')
                ->get();
        // $posts = Post::where('genre_index', $genreIndex)
        //         ->with(['user', 'tags', 'postImages', 'donmais', 'comments.replies'])
        //         ->orderBy('created_at', 'desc')
        //         ->get();

        foreach ($posts as $post) {
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



    // 全ジャンルの取得
    public function getGenres()
    {
        return Post::$genres;
    }



    // 投稿
    public function create(Request $request)
    {
        // バリデーション
        $request->validate(Post::$postRules, Post::$postValMessages);

        // 投稿者のidを取得
        $userId = Auth::id();

        // bodyを取得
        $body = $request->body;

        // genre_indexを取得
        $genreIndex = $request->genreIndex;

        // タグの保存
        $tagsText = $request->tags;
        $tagsList = preg_match_all('/#([^\s#]+)/', str_replace('　', ' ', $tagsText), $m) ? $m[1] : [];     // タグの配列
        $tags = [];     // タグのレコードの配列
        foreach ($tagsList as $tag) {
            $record = Tag::firstOrCreate(['name' => $tag]);
            array_push($tags, $record);
        }
        $tags_id = [];      // この投稿が持つタグのidの配列
        foreach ($tags as $tag) {
            array_push($tags_id, $tag['id']);
        }

        // 投稿作成
        $post = new Post;
        $post->user_id = $userId;
        $post->body = $body;
        $post->genre_index = $genreIndex;
        $post->save();
        
        $post->tags()->attach($tags_id);

        // 画像アップロードと画像のパスをpost_imagesテーブルに保存
        $images = $request->file('images');

        if ($images) {
            foreach ($images as $image) {
                $path = Storage::disk('s3')
                        ->putFile('post_images', $image, 'public');
                PostImage::create([
                    'post_id' => $post->id,
                    'path' => Storage::disk('s3')->url($path),
                ]);
            }
        }
    }

}
