<?php

use Illuminate\Http\Request;

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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });
    
Route::middleware('auth:sanctum')->group(function(){
    // 認証ユーザー情報取得
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ユーザー関連
    Route::post('/user/password', 'UserController@password');
    Route::post('/user/edit', 'UserController@update');
    Route::get('/user/{id}', 'UserController@show');

    // フォロー関連
    Route::get('/following/{id}', 'FollowController@followingShow');
    Route::get('/follower/{id}', 'FollowController@followerShow');
    Route::post('/follow', 'FollowController@follow');
    Route::post('/unfollow', 'FollowController@unfollow');

    // ヘッダー
    Route::get('/headinit', 'PostController@headInit');

    // ホーム
    Route::get('/home/init', 'PostController@homeInit');
    Route::get('/genre', 'PostController@getGenres');
    Route::post('/post/add', 'PostController@create');
    Route::get('/home/genre/{name}', 'PostController@homeGnereInit');

    // どんまい
    Route::get('/donmai/users/{id}', 'DonmaiController@getDonmaiUser');
    Route::post('/donmai/{id}', 'DonmaiController@donmai');
    Route::post('/undonmai/{id}', 'DonmaiController@unDonmai');

    // コメント
    Route::get('/comments/get/{id}', 'CommentController@getComments');
    Route::post('/comment', 'CommentController@create');
    Route::post('/comment/good/{id}', 'CommentController@good');
    Route::post('/comment/ungood/{id}', 'CommentController@ungood');
    
    // コメントへの返信
    Route::post('/comment/reply', 'ReplyController@create');
    Route::post('/comment/reply/good/{id}', 'ReplyController@good');
    Route::post('/comment/reply/ungood/{id}', 'ReplyController@ungood');
    
});

// ログイン関連
Route::post('/register', 'RegisterController@register');
Route::post('/login', 'LoginController@login');
// ログアウト
Route::post('/logout', 'LoginController@logout');
