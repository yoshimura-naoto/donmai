<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\Events\GuchiCreated;
use App\Events\GuchiDeleted;

use App\GuchiRoom;
use App\GuchiBookmark;
use App\Guchi;
use App\GuchiImage;
use App\GuchiGood;
use App\User;
use App\Post;
use Storage;


class GuchiController extends Controller
{
    // グチのジャンル一覧を取得
    public function guchiRoomGenreGet()
    {
        return Post::$genres;
    }


    // グチ部屋（チャットルーム）の作成
    public function roomCreate(Request $request)
    {
        // バリデーション
        $request->validate(GuchiRoom::$guchiRoomRules, GuchiRoom::$guchiRoomsValMessages);

        // アイコン画像保存処理
        if ($request->icon) {
            $image = $request->file('icon');
            $path = Storage::disk('s3')->putFile('guchiroom_icon', $image, 'public');
            $iconUrl = Storage::disk('s3')->url($path);
        } else {
            $iconUrl = null;
        }

        GuchiRoom::create([
            'icon' => $iconUrl,
            'title' => $request->title,
            'genre' => $request->genre,
            'user_id' => Auth::id(),
        ]);
    }



    // グチ部屋の削除
    public function roomDelete($id)
    {
        $guchiRoom = GuchiRoom::where('id', $id)
                            ->with(['guchiImages'])
                            ->first();

        // アイコン画像削除
        if (count($guchiRoom->guchiImages) > 0) {
            foreach ($guchiRoom->guchiImages as $image) {
                Storage::disk('s3')->delete(parse_url($image->path, PHP_URL_PATH));
            }
        }

        $guchiRoom->delete();
    }



    // グチ部屋のブックマーク
    public function bookmark($id)
    {
        GuchiBookmark::create([
            'user_id' => Auth::id(),
            'guchi_room_id' => $id,
        ]);
    }


    // グチ部屋のブックマークのキャンセル
    public function unBookmark($id)
    {
        GuchiBookmark::where('user_id', Auth::id())
                        ->where('guchi_room_id', $id)
                        ->delete();
    }



    // グチ部屋全部を新着順で取得（１０件ずつページネーション）
    public function getRoomsNew(Request $request)
    {
        $rooms = GuchiRoom::with(['guchiBookmarks' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount(['guchis'])
                            ->orderBy('id', 'desc')
                            ->paginate(10);

        // 全ジャンルを取得
        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            // 認証ユーザーがブックマークしているかどうか
            if (count($room->guchiBookmarks) > 0) {
                $room->bookmarked = true;
            } else {
                $room->bookmarked = false;
            }
        }

        return $rooms;
    }



    // グチ部屋全部をグチが多い順で取得
    public function getRoomsTrend()
    {
        $rooms = GuchiRoom::with(['guchiBookmarks' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount(['guchis'])
                            ->orderBy('guchis_count', 'desc')
                            ->paginate(10);

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            if (count($room->guchiBookmarks) > 0) {
                $room->bookmarked = true;
            } else {
                $room->bookmarked = false;
            }
        }

        return $rooms;
    }



    // グチ部屋をジャンル別に新着順で取得
    public function getRoomsGenreNew($name)
    {
        $rooms = GuchiRoom::where('genre', $name)
                            ->with(['guchiBookmarks' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount(['guchis'])
                            ->orderBy('id', 'desc')
                            ->paginate(5);

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            if (count($room->guchiBookmarks) > 0) {
                $room->bookmarked = true;
            } else {
                $room->bookmarked = false;
            }
        }

        return $rooms;
    }



    // グチ部屋をジャンル別に人気順で取得
    public function getRoomsGenreTrend($name)
    {
        $rooms = GuchiRoom::where('genre', $name)
                            ->with(['guchiBookmarks' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount(['guchis'])
                            ->orderBy('guchis_count', 'desc')
                            ->paginate(5);

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            if (count($room->guchiBookmarks) > 0) {
                $room->bookmarked = true;
            } else {
                $room->bookmarked = false;
            }
        }

        return $rooms;
    }



    // 検索したグチ部屋を新着順で取得
    public function getSearchGuchiroomNew($word)
    {
        $authId = Auth::id();

        $rooms = GuchiRoom::where('title', 'like', '%' . $word . '%')
                            ->withCount(['guchis', 'guchiBookmarks' => function (Builder $query) use ($authId) {
                                $query->where('user_id', $authId);
                            }])
                            ->orderBy('id', 'desc')
                            ->paginate(5);

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            if ($room->guchi_bookmarks_count) {
                $room->bookmarked = true;
            } else {
                $room->bookmarked = false;
            }
        }

        return $rooms;
    }



    // 検索したグチ部屋を人気順で取得
    public function getSearchGuchiroomPopular($word)
    {
        $authId = Auth::id();

        $rooms = GuchiRoom::where('title', 'like', '%' . $word . '%')
                            ->withCount(['guchis', 'guchiBookmarks' => function (Builder $query) use ($authId) {
                                $query->where('user_id', $authId);
                            }])
                            ->orderBy('guchis_count', 'desc')
                            ->paginate(5);

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            if ($room->guchi_bookmarks_count) {
                $room->bookmarked = true;
            } else {
                $room->bookmarked = false;
            }
        }

        return $rooms;
    }



    // ユーザーがブックマークしたグチ部屋をブックマークした日付が新しい順で取得（５件ずつページネーション）
    public function getUserGuchiroom($id)
    {
        $guchiBookmarks = GuchiBookmark::where('user_id', $id)
                                    ->with(['guchiRoom' => function ($query) {
                                        $query->withCount(['guchis']);
                                    }])
                                    ->orderBy('id', 'desc')
                                    ->paginate(5);

        $genres = Post::$genres;

        foreach ($guchiBookmarks as $bookmark) {
            $bookmark->guchiRoom->genre = array_column($genres, 'name', 'route')[$bookmark->guchiRoom->genre];
            $bookmark->guchiRoom->bookmarked = true;
        }

        return $guchiBookmarks;
    }



    // グチ詳細ページ（各スレ）用に認証ユーザー情報とグチ部屋情報を取得する
    public function init($id)
    {
        $guchiRoom = GuchiRoom::where('id', $id)->first();

        $data = [
            'authUser' => Auth::user(),
            'guchiRoom' => $guchiRoom,
        ];

        return $data;
    }



    // グチの投稿
    public function guchiCreate(Request $request)
    {
        $request->validate(Guchi::$guchiRules, Guchi::$guchiValMessages);

        // 匿名かどうかの決定
        $anonymous = false;
        if ($request->anonymous) {
            $anonymous = true;
            $userId = null;
        } else {
            $userId = Auth::id();
        }

        // グチのレコード作成
        $guchi = Guchi::create([
            'user_id' => $userId,
            'guchi_room_id' => $request->roomId,
            'body' => $request->body,
            'anonymous' => $anonymous,
        ]);

        // 画像の保存
        $images = $request->file('images');

        if ($images) {
            foreach ($images as $image) {
                $path = Storage::disk('s3')
                                ->putFile('guchi_images', $image, 'public');
                GuchiImage::create([
                    'guchi_id' => $guchi->id,
                    'path' => Storage::disk('s3')->url($path),
                ]);
            }
        }

        event(new GuchiCreated($guchi));
    }



    // グチの削除
    public function guchiDelete($id)
    {
        $guchi = Guchi::where('id', $id)
                    ->with(['guchiImages'])
                    ->first();
        
        if (count($guchi->guchiImages) > 0) {
            foreach ($guchi->guchiImages as $image) {
                Storage::disk('s3')->delete(parse_url($image->path, PHP_URL_PATH));
            }
        }

        $guchi->delete();

        $guchiData = [
            'id' => $guchi->id,
            'guchi_room_id' => $guchi->guchi_room_id,
        ];

        event(new GuchiDeleted($guchiData));
    }


    // グチにいいね
    public function good($id)
    {
        GuchiGood::create([
            'user_id' => Auth::id(),
            'guchi_id' => $id,
        ]);
    }


    // グチへのいいねのキャンセル
    public function ungood($id)
    {
        GuchiGood::where('user_id', Auth::id())
                ->where('guchi_id', $id)
                ->delete();
    }



    // あるグチ部屋で最新のグチを１件取得
    public function getLatestGuchi($id)
    {
        $guchi = Guchi::where('guchi_room_id', $id)
                        ->with(['user:id,name,icon', 'guchiImages',])
                        ->withCount(['guchiGoods', 'guchiImages'])
                        ->orderBy('id', 'desc')
                        ->first();

        // 自分がいいねしているかどうか
        $guchi->gooded = false;

        // 自分のグチかどうか
        if ($guchi->user_id === Auth::id()) {
            $guchi->isSelf = true;
        } else {
            $guchi->isSelf = false;
        }

        return $guchi;
    }


    // グチ部屋のグチ（発言）の最新５件を古い順で取得（５件ずつ無限スクロール）
    public function guchiGet($id, Request $request)
    {
        $guchis = Guchi::where('guchi_room_id', $id)
                        ->with(['user:id,name,icon', 'guchiImages',])
                        ->withCount(['guchiImages',
                                     'guchiGoods',
                                     'guchiGoods as guchi_good_by_user' => function (Builder $query) {
                                        $query->where('user_id', Auth::id());
                                    }])
                        ->orderBy('id', 'desc')
                        ->offset($request->loaded_guchis_count)
                        ->limit(5)
                        ->get();

        $imagesCount = 0;  // 今回読み込む画像の合計数

        foreach ($guchis as $guchi) {
            // 認証ユーザーがいいねしてるかどうか
            if ($guchi->guchi_good_by_user) {
                $guchi->gooded = true;
            } else {
                $guchi->gooded = false;
            }
            // 自分のグチかどうか
            if ($guchi->user_id === Auth::id()) {
                $guchi->isSelf = true;
            } else {
                $guchi->isSelf = false;
            }
            // 各投稿の画像数を足し合わせる
            $imagesCount += $guchi->guchi_images_count;
        }

        $data = [
            'guchis' => $guchis->sort()->values()->all(),
            'imagesCount' => $imagesCount,
            'guchisTotal' => Guchi::where('guchi_room_id', $id)->count(),
        ];

        return $data;
    }
}
