<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

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


    // グチ部屋の作成
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



    // グチ部屋全部を新着順で取得
    public function getRoomsNew()
    {
        $rooms = GuchiRoom::with(['guchiBookmarks' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount(['guchis'])
                            ->orderBy('created_at', 'desc')
                            ->get();

        // 全ジャンルを取得
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



    // グチ部屋全部を人気順で取得
    public function getRoomsTrend()
    {
        $rooms = GuchiRoom::with(['guchiBookmarks' => function ($query) {
                                $query->where('user_id', Auth::id());
                            }])
                            ->withCount(['guchis'])
                            ->orderBy('guchis_count', 'desc')
                            ->get();

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            // $room->genre = $genres[array_search($room->genre, array_column($genres, 'route'))]['name'];
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            // $room->guchis_count = 5;
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
                            ->orderBy('created_at', 'desc')
                            ->get();

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            // $room->genre = $genres[array_search($name, array_column($genres, 'route'))]['name'];
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            // $room->guchis_count = 314;
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
                            ->get();

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
                            ->orderBy('created_at', 'desc')
                            ->get();

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
                            ->get();

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



    // ユーザーがブックマークしたグチ部屋を取得
    public function getUserGuchiroom($id)
    {
        $rooms = GuchiRoom::whereHas('guchiBookmarks', function (Builder $query) use ($id) {
                            $query->where('user_id', $id);
                        })
                        ->withCount(['guchis'])
                        ->get();

        $genres = Post::$genres;

        foreach ($rooms as $room) {
            $room->genre = array_column($genres, 'name', 'route')[$room->genre];
            $room->bookmarked = true;
        }

        return $rooms;
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



    // グチ詳細ページの初期化
    public function init($id)
    {
        $guchiRoom = GuchiRoom::where('id', $id)
                            ->with(['guchis.user', 'guchis.guchiImages', 'guchis' => function ($query) {
                                $query->withCount(['guchiGoods',
                                                   'guchiGoods as guchi_good_by_user' => function (Builder $query) {
                                                       $query->where('user_id', Auth::id());
                                                   }]);
                            }])
                            ->first();

        foreach ($guchiRoom->guchis as $guchi) {
            if ($guchi->guchi_good_by_user) {
                $guchi->gooded = true;
            } else {
                $guchi->gooded = false;
            }
        }

        $data = [
            'authUser' => Auth::user(),
            'guchiRoom' => $guchiRoom,
            'guchis' => $guchiRoom->guchis,
        ];

        return $data;
    }



    // グチのみを取得
    public function guchiGet($id)
    {
        $guchis = Guchi::where('guchi_room_id', $id)
                        ->with(['user', 'guchiImages',])
                        ->withCount(['guchiGoods',
                                     'guchiGoods as guchi_good_by_user' => function (Builder $query) {
                                        $query->where('user_id', Auth::id());
                                    }])
                        ->get();

        foreach ($guchis as $guchi) {
            // $guchi->guchiGoods_count = 0;
            if ($guchi->guchi_good_by_user) {
                $guchi->gooded = true;
            } else {
                $guchi->gooded = false;
            }
        }

        return $guchis;
    }



    // グチの投稿
    public function guchiCreate(Request $request)
    {
        $request->validate(Guchi::$guchiRules, Guchi::$guchiValMessages);

        // 匿名の決定
        $anonymous = false;

        if ($request->anonymous) {
            $anonymous = true;
        }

        // グチのレコード作成
        $guchi = Guchi::create([
            'user_id' => Auth::id(),
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
}
