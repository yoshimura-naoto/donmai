<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:sanctum')->group(function(){
    // 認証ユーザー情報取得
    Route::get('/user', function (Request $request) {
        $user = Auth::user()->makeVisible(['email']);
        return $user;
        // return $request->user();
    });

    // 認証ユーザー情報とジャンルを取得
    Route::get('/authandgenre', 'PostController@getAuthAndGenre');

    // ユーザー関連
    Route::post('/user/password', 'UserController@password');
    Route::post('/user/edit', 'UserController@update');
    Route::get('/user/{id}', 'UserController@show');
    Route::get('/post/user/get/{id}', 'PostController@getUserPostsOnly');
    Route::get('/post/user/donmai/{id}', 'PostController@getUserDonmaiPostsOnly');
    Route::get('/guchiroom/user/{id}', 'GuchiController@getUserGuchiroom');
    
    // フォロー関連
    Route::get('/following/{id}', 'FollowController@followingShow');
    Route::get('/follower/{id}', 'FollowController@followerShow');
    Route::post('/follow', 'FollowController@follow');
    Route::post('/unfollow', 'FollowController@unfollow');
        
    // ホーム
    Route::get('/genre', 'PostController@getGenres');
    Route::get('/post/get', 'PostController@getPosts');
    Route::get('/post/genre/get/{name}', 'PostController@getGenrePosts');
    Route::post('/post/add', 'PostController@create');
    Route::post('/post/edit', 'PostController@update');
    Route::post('/post/delete/{id}', 'PostController@delete');

    // どんまい
    Route::get('/donmai/users/{id}', 'DonmaiController@getDonmaiUser');
    Route::post('/donmai/{id}', 'DonmaiController@donmai');
    Route::post('/undonmai/{id}', 'DonmaiController@unDonmai');

    // コメント
    Route::get('/comments/get/{id}', 'CommentController@getComments');
    Route::post('/comment', 'CommentController@create');
    Route::post('/comment/delete/{id}', 'CommentController@delete');
    Route::post('/comment/good/{id}', 'CommentController@good');
    Route::post('/comment/ungood/{id}', 'CommentController@ungood');
    
    // コメントへの返信
    Route::get('/replies/get/{id}', 'ReplyController@get');
    Route::post('/comment/reply', 'ReplyController@create');
    Route::post('/reply/delete/{id}', 'ReplyController@delete');
    Route::post('/comment/reply/good/{id}', 'ReplyController@good');
    Route::post('/comment/reply/ungood/{id}', 'ReplyController@ungood');
    
    // トレンド
    Route::get('/tag/trend', 'TagController@getTrend');

    // タグ
    Route::get('/tag/count/{name}', 'TagController@tagCount');
    Route::get('/post/tags/new/{name}', 'TagController@getTagsNewPosts');
    Route::get('/post/tags/popular/{name}', 'TagController@getTagsPopularPosts');

    // 話題の投稿
    Route::get('/post/hot/get', 'PostController@getHotPosts');

    // みんなでグチ
    Route::get('/guchiroom/genres', 'GuchiController@guchiRoomGenreGet');
    Route::post('/guchiroom/create', 'GuchiController@roomCreate');
    Route::post('/guchiroom/delete/{id}', 'GuchiController@roomDelete');
    Route::get('/guchiroom/all/new', 'GuchiController@getRoomsNew');
    Route::get('/guchiroom/all/trend', 'GuchiController@getRoomsTrend');
    Route::get('/guchiroom/genre/new/{name}', 'GuchiController@getRoomsGenreNew');
    Route::get('/guchiroom/genre/trend/{name}', 'GuchiController@getRoomsGenreTrend');
    Route::post('/guchiroom/bookmark/{id}', 'GuchiController@bookmark');
    Route::post('/guchiroom/unbookmark/{id}', 'GuchiController@unBookmark');
    Route::get('/guchi/init/{id}', 'GuchiController@init');
    Route::get('/guchi/get/{id}', 'GuchiController@guchiGet');
    Route::get('/guchi/latest/{id}', 'GuchiController@getLatestGuchi');
    Route::post('/guchi/create', 'GuchiController@guchiCreate');
    Route::post('/guchi/delete/{id}', 'GuchiController@guchiDelete');
    Route::post('/guchi/good/{id}', 'GuchiController@good');
    Route::post('/guchi/ungood/{id}', 'GuchiController@ungood');

    // 検索
    Route::get('/post/search/new/{word}','PostController@getSearchNewPostsOnly');
    Route::get('/post/search/popular/{word}','PostController@getSearchPopularPostsOnly');
    Route::get('/search/guchiroom/new/{word}','GuchiController@getSearchGuchiroomNew');
    Route::get('/search/guchiroom/popular/{word}','GuchiController@getSearchGuchiroomPopular');
    Route::get('/search/users/{word}','UserController@getSearchUsers');
});

// 登録
Route::post('/register', 'RegisterController@register');
// 退会
Route::post('/unsubscribe/{id}', 'RegisterController@unsubscribe');
// ログイン
Route::post('/login', 'LoginController@login');
// ログアウト
Route::post('/logout', 'LoginController@logout');