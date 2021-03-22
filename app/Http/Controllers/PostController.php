<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use InterventionImage;

use Carbon\Carbon;

use App\User;
use App\Post;
use App\PostImage;
use App\Tag;
use App\PostTag;
use App\Donmai;
use App\Reply;
use Storage;


class PostController extends Controller
{
    // 認証ユーザー情報とジャンルを取得
    public function getAuthAndGenre()
    {
        $data = [
            'authUser' => Auth::user(),
            'genres' => Post::$genres,
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

        DB::transaction(function () use ($request) {
            // タグの保存
            $tagsText = $request->tags;   // ユーザーが入力したタグを文字列の状態（'#りんご #バナナ'）で取得
            $tagsList = preg_match_all('/#([^\s#]+)/', str_replace('　', ' ', $tagsText), $m) ? $m[1] : [];  // $tagsTextを配列に変換（ハッシュタグから始まる単語を取得して配列に）
            $uniqueTagsList = array_unique($tagsList);  // 重複を除去
            $tags = [];     // タグのレコード（tagsテーブルのレコード）の配列
            foreach ($uniqueTagsList as $tag) {
                $record = Tag::firstOrCreate(['name' => $tag]);
                array_push($tags, $record);
            }
            $tags_id = [];  // この投稿が持つタグのidの配列
            foreach ($tags as $tag) {
                array_push($tags_id, $tag['id']);
            }
    
            // 投稿作成
            $post = new Post;
            $post->user_id = Auth::id();
            $post->body = $request->body;
            $post->genre_index = $request->genreIndex;
            $post->save();
            
            // タグと投稿の紐つけ（post_tagレコード作成）
            $post->tags()->attach($tags_id);
    
            // 画像アップロードと画像のパスをpost_imagesテーブルに保存
            $images = $request->file('images');
    
            if ($images) {
                foreach ($images as $image) {
                    InterventionImage::make($image)
                        ->encode('jpg')
                        ->save($image);
                    $path = Storage::disk('s3')
                            ->putFile('post_images', $image, 'public');
                    PostImage::create([
                        'post_id' => $post->id,
                        'path' => Storage::disk('s3')->url($path),
                    ]);
                }
            }
        });
    }



    // 投稿の編集
    public function update(Request $request)
    {
        $request->validate(Post::$postRules, Post::$postValMessages);

        $post = Post::where('id', $request->id)
                    ->with(['tags' => function ($query) {
                        $query->withCount(['posts']);
                    }])
                    ->first();

        DB::transaction(function () use ($request, $post) {
            // 本文の更新
            $post->body = $request->body;
    
            // ジャンルの更新
            $post->genre_index = $request->genreIndex;
    
            // タグの更新
            $tagsText = $request->tags;
            $newTags = preg_match_all('/#([^\s#]+)/', str_replace('　', ' ', $tagsText), $m) ? $m[1] : [];  // リクエストで取得したタグの配列
            $oldTags = array_column($post->tags->all(), 'name');  // 今までこの投稿に紐ついていたタグの配列
            // 今までのタグで削除するものがあれば削除
            $deleteOldTags = array_values(array_diff($oldTags, $newTags)); // oldTagsにあってnewTagsにないタグ名の配列（消すタグ名の配列）
            $deleteOldTagRecords = $post->tags
                                        ->whereIn('name', $deleteOldTags)
                                        ->all();  // 消すtagsテーブルのレコードの配列 
            // 紐つく投稿が0になる場合はそのタグを削除
            foreach ($post->tags->whereIn('name', $deleteOldTags) as $tag) {
                if ($tag->posts_count === 1) {
                    $tag->delete();
                }
            }
            // 中間テーブルpost_tagで削除するものを削除
            $post->tags()->detach(array_column($deleteOldTagRecords, 'id'));
            // 新しく追加するタグを作成
            $newAddTag = array_values(array_diff($newTags, $oldTags));  // newTagsにあってoldTagsにないタグ名の配列（新しく追加するタグ名の配列）
            $tags = [];  // タグ(tagsテーブル)のレコードの配列
            foreach ($newAddTag as $tag) {
                $record = Tag::firstOrCreate(['name' => $tag]); // tagsテーブルのレコード作成
                array_push($tags, $record);
            }
            $tags_id = [];  // この投稿が持つタグのidの配列
            foreach ($tags as $tag) {
                array_push($tags_id, $tag['id']);
            }
            // タグと投稿の紐つけ（中間テーブルpost_tagレコード作成）
            $post->tags()->attach($tags_id);
    
            // 既存の画像で削除するものがあれば削除
            if ($request->deleteOldImagesId) {
                $postImages = PostImage::whereIn('id', $request->deleteOldImagesId)->get();
                foreach ($postImages as $postImage) {
                    Storage::disk('s3')->delete(parse_url($postImage->path, PHP_URL_PATH));
                }
                PostImage::whereIn('id', $request->deleteOldImagesId)->delete();
            }
            // 新たにアップロードする画像の保存
            $images = $request->file('newImageFiles');
            if ($images) {
                foreach ($images as $image) {
                    InterventionImage::make($image)
                        ->encode('jpg')
                        ->save($image);
                    $path = Storage::disk('s3')
                                    ->putFile('post_images', $image, 'public');
                    PostImage::create([
                        'post_id' => $post->id,
                        'path' => Storage::disk('s3')->url($path),
                    ]);
                }
            }
    
            // 投稿の更新
            $post->save();
        });


        // 編集した投稿をレスポンスとして返す
        $post = Post::where('id', $request->id)
                    ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }])
                    ->withCount('donmais', 'comments', 'replies')
                    ->first();

        $post->genre = Post::$genres[$post->genre_index];
        if (count($post->donmais) > 0) {
            $post->donmai = true;
        } else {
            $post->donmai = false;
        }
        $post->donmaiCount = $post->donmais_count;
        $post->commentCount = $post->comments_count + $post->replies_count;
        $post->postMenuOpened = false;

        return response()->json([
            'post' => $post,
        ]);
    }



    // 投稿の削除
    public function delete($id)
    {
        $post = Post::where('id', $id)
                    ->with(['postImages', 'tags' => function ($query) {
                        $query->withCount(['posts']);
                    }])
                    ->first();

        DB::transaction(function () use ($post) {
            // 画像を削除
            if (count($post->postImages) > 0) {
                foreach ($post->postImages as $image) {
                    Storage::disk('s3')->delete(parse_url($image->path, PHP_URL_PATH));
                }
            }
    
            // この投稿を削除した際、どの投稿にも紐つけられないタグが発生する場合はそのタグを削除
            foreach ($post->tags as $tag) {
                if ($tag->posts_count === 1) {
                    $tag->delete();
                }
            }
    
            $post->delete();
        });
    }



    // フォロー中のユーザーの投稿優先で、投稿を新着順で全て取得（３件ずつ無限スクロール）
    public function getPosts(Request $request)
    {
        $authUser = Auth::User();
        $followsId = array_column($authUser->follows->all(), 'following_user_id');  // 認証ユーザーがフォローしているユーザーのIDの配列
        array_push($followsId, Auth::id());  // 自分のIDも追加

        // $yesterday = new Carbon('-1 day', 'Asia/Tokyo');

        // 認証ユーザーがフォローしてるユーザーの合計投稿数
        $followPostsCount = Post::whereIn('user_id', $followsId)
                                // ->where('created_at', '>=', $yesterday)  // もしサービスを本格運用するなら１日以内の投稿数のみ取得
                                ->count();

        $loadedPostIds = array_map('intval', explode('-', $request->loaded_post_ids));  // すでに読み込まれた投稿のIDの配列
        if ($loadedPostIds[0] === 0) $loadedPostIds = [];  // 空の場合に配列に0が加わるのを防ぐ

        if (count($loadedPostIds) < $followPostsCount) {
            // 先に認証ユーザーがフォローしているユーザーの投稿と自分の投稿を新着順で取得
            $posts = Post::whereIn('user_id', $followsId)
                        ->whereNotIn('id', $loadedPostIds)
                        ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                            $query->where('user_id', Auth::id());
                        }])
                        ->withCount('donmais', 'comments', 'replies')
                        ->orderBy('id', 'desc')
                        ->limit(3)
                        ->get();
        } else {
            // フォロー中のユーザーの投稿を全て読み終えたらフォローしていないユーザーの投稿を新着順で取得
            $posts = Post::whereNotIn('user_id', $followsId)
                        ->whereNotIn('id', $loadedPostIds)
                        ->with(['user:id,name,icon', 'tags', 'postImages', 'donmais' => function ($query) {
                            $query->where('user_id', Auth::id());
                        }])
                        ->withCount('donmais', 'comments', 'replies')
                        ->orderBy('id', 'desc')
                        ->limit(3)
                        ->get();
        }

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
            $post->postMenuOpened = false;
        }

        $data = [
            'posts' => $posts,
            'postsTotal' => Post::count(),
        ];

        return $data;
    }


    // ジャンル別で投稿を取得（３件ずつ無限スクロール）
    public function getGenrePosts($name, Request $request)
    {
        // ジャンルのインデックスを取得
        $genreRoutes = array_column(Post::$genres, 'route');
        $genreIndex = array_search($name, $genreRoutes);

        // そのジャンルの投稿、投稿したユーザー、タグ、画像、を取得
        $posts = Post::where('genre_index', $genreIndex)
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
            'postsTotal' => Post::where('genre_index', $genreIndex)->count(),
        ];

        return $data;
    }



    // 話題の投稿ページで、ここ１週間以内でどんまい数が多い順に投稿を取得
    public function getHotPosts(Request $request)
    {
        // $lastWeek = new Carbon('-7 day', 'Asia/Tokyo');
        $lastWeek = new Carbon('-200 day', 'Asia/Tokyo');  // 今だけ-200日に設定。
    
        $loadedPostIds = array_map('intval', explode('-', $request->loaded_post_ids));  // すでに読み込まれた投稿のIDの配列

        // 投稿一覧をdonmais数が多い順で取得（３件ずつ無限スクロール）
        $posts = Post::where('created_at', '>=', $lastWeek)
                    ->whereNotIn('id', $loadedPostIds)
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
            'postsTotal' => Post::where('created_at', '>=', $lastWeek)->count(),
        ];

        return $data;
    }



    // ユーザーページでユーザーの投稿を新着順で取得（３件ずつ無限スクロール）
    public function getUserPostsOnly($id, Request $request)
    {
        $posts = Post::where('user_id', $id)
                    ->with(['user:id,name,icon', 'tags', 'postImages'])
                    ->withCount(['donmais', 'comments', 'replies',
                                 'donmais as donmai_by_user' => function (Builder $query) {
                                    $query->where('user_id', Auth::id());
                                }])
                    ->orderBy('id', 'desc')
                    ->offset($request->loaded_posts_count)
                    ->limit(3)
                    ->get();

        foreach ($posts as $post) {
            $post->genre = Post::$genres[$post->genre_index];
            // 認証ユーザーがどんまいしているかチェック
            if ($post->donmai_by_user) {
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
            'postsTotal' => Post::where('user_id', $id)->count(),
        ];

        return $data;
    }



    // ユーザーがどんまいした投稿を、どんまいした日が新しい順に取得（３件ずつ無限スクロール）
    public function getUserDonmaiPostsOnly($id, Request $request)
    {
        $donmais = Donmai::where('user_id', $id)
                        ->with(['post.user:id,name,icon',
                                'post.tags', 
                                'post.postImages',
                                'post' => function ($query) {
                                    $query->withCount(['donmais', 'comments', 'replies']);
                                }])
                        ->orderBy('id', 'desc')
                        ->offset($request->loaded_posts_count)
                        ->limit(3)
                        ->get();

        foreach ($donmais as $donmai) {
            $donmai->post->genre = Post::$genres[$donmai->post->genre_index];
            $donmai->post->donmai = true;
            $donmai->post->donmaiCount = $donmai->post->donmais_count;
            $donmai->post->commentCount = $donmai->post->comments_count + $donmai->post->replies_count;
            $donmai->post->postMenuOpened = false;
        }

        $data = [
            'donmais' => $donmais,
            'postsTotal' => Post::whereHas('donmais', function (Builder $query) use ($id) {
                                    $query->where('user_id', $id);
                                })
                                ->count(),
        ];

        return $data;
    }



    // 検索したワードをタグに含む投稿を新着順で取得（３件ずつ無限スクロール）
    public function getSearchNewPostsOnly($word, Request $request)
    {
        $posts = Post::whereHas('tags', function (Builder $query) use ($word) {
                        $query->where('name', $word);
                    })
                    ->with(['user:id,name,icon', 'tags', 'postImages'])
                    ->withCount(['donmais', 'comments', 'replies',
                                 'donmais as donmai_by_user' => function (Builder $query) {
                                    $query->where('user_id', Auth::id());
                                 }])
                    ->orderBy('id', 'desc')
                    ->offset($request->loaded_posts_count)
                    ->limit(3)
                    ->get();

        foreach ($posts as $post) {
            $post->genre = Post::$genres[$post->genre_index];
            // 認証ユーザーがどんまいしているかチェック
            if ($post->donmai_by_user) {
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
            'postsTotal' => Post::whereHas('tags', function (Builder $query) use ($word) {
                                $query->where('name', $word);
                            })
                            ->count(),
        ];

        return $data;
    }



    // 検索したワードをタグに含む投稿をどんまい数が多い順で取得（３件ずつ無限スクロール）
    public function getSearchPopularPostsOnly($word, Request $request)
    {
        $loadedPostIds = array_map('intval', explode('-', $request->loaded_post_ids));  // すでに読み込まれた投稿のIDの配列

        $posts = Post::whereNotIn('id', $loadedPostIds)
                    ->whereHas('tags', function (Builder $query) use ($word) {
                        $query->where('name', $word);
                    })
                    ->with(['user:id,name,icon', 'tags', 'postImages'])
                    ->withCount(['donmais', 'comments', 'replies',
                                 'donmais as donmai_by_user' => function (Builder $query) {
                                    $query->where('user_id', Auth::id());
                                 }])
                    ->orderBy('donmais_count', 'desc')
                    ->orderBy('comments_count', 'desc')
                    // ->offset($request->loaded_posts_count)
                    ->limit(3)
                    ->get();

        foreach ($posts as $post) {
            $post->genre = Post::$genres[$post->genre_index];
            // 認証ユーザーがどんまいしているかチェック
            if ($post->donmai_by_user) {
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
            'postsTotal' => Post::whereHas('tags', function (Builder $query) use ($word) {
                                $query->where('name', $word);
                            })
                            ->count(),
        ];

        return $data;
    }
}
