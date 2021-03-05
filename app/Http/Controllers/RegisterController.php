<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Tag;
use App\GuchiRoom;
use App\Guchi;

use Storage;


class RegisterController extends Controller
{
    // ユーザー登録の処理
    public function register(Request $request)
    {
        $form = $request->
                validate(User::$registerRules, User::$registerValMessages);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }


    // 退会
    public function unsubscribe($id)
    {
        $user = User::where('id', $id)
                    ->with(['guchiRooms', 'guchis', 'postImages', 'posts.tags' => function ($query) {
                        $query->withCount(['posts']);
                    }])
                    ->first();

        // このユーザーの投稿を全て削除し、それに紐ついたタグとの関係も削除したとき、どの投稿にも紐つかないタグが発生する場合はそのタグを削除
        $tagsId = [];
        foreach ($user->posts as $post) {
            foreach ($post->tags as $tag) {
                if ($tag->posts_count === 1) {
                    array_push($tagsId, $tag->id);
                }
            }
        }
        Tag::whereIn('id', $tagsId)->delete();

        // このユーザーが作成したグチ部屋のuser_idをnullにする（グチ部屋自体は消さない）
        GuchiRoom::where('user_id', $id)->update(['user_id' => null]);

        // このユーザーが作成したグチのuser_idをnullにする（グチ自体は消さない）
        Guchi::where('user_id', $id)->update(['user_id' => null]);

        // プロフィール画像の削除
        Storage::disk('s3')->delete(parse_url($user->icon, PHP_URL_PATH));

        // このユーザーの各投稿に紐ついた画像を削除
        foreach ($user->postImages as $image) {
            Storage::disk('s3')->delete(parse_url($image->path, PHP_URL_PATH));
        }

        $user->delete();
    }
}
