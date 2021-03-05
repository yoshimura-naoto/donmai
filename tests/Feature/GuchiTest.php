<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Guchi;
use App\GuchiImage;
use App\GuchiGood;
use App\GuchiRoom;
use App\GuchiBookmark;

class GuchiTest extends TestCase
{
    use RefreshDatabase;

    // グチのジャンル一覧を取得
    public function testGuchiRoomGenreGet()
    {
        $user = factory(User::class)->create();

        $this->getJson('/api/guchiroom/genres')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchiroom/genres')
            ->assertOk()
            ->assertSeeInOrder([
                'jobs',
                'dozi',
                'love',
                'game',
                'disease',
            ]);
    }


    // グチ部屋（チャットルーム）の作成
    public function testRoomCreate()
    {
        $user = factory(User::class)->create();

        $form = [
            'title' => 'ゲーム全然勝てない！',
            'genre' => 'game',
            'user_id' => $user->id,
        ];
        $form2 = [
            'title' => '',
            'genre' => 'game',
            'user_id' => $user->id,
        ];
        $form3 = [
            'title' => 'ゲーム全然勝てない！',
            'genre' => '',
            'user_id' => $user->id,
        ];

        $this->postJson('/api/guchiroom/create', $form)
            ->assertStatus(401);

        // タイトル未入力によるバリデーションエラー
        $this->actingAs($user)
            ->postJson('/api/guchiroom/create', $form2)
            ->assertStatus(422);

        // ジャンル未選択によるバリデーションエラー
        $this->postJson('/api/guchiroom/create', $form3)
            ->assertStatus(422);

        // 成功パターン
        $this->postJson('/api/guchiroom/create', $form)
            ->assertOk();
        
        $this->assertDatabaseHas('guchi_rooms', $form);
    }


    // グチ部屋の削除
    public function testGuchiRoomDelete()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('guchi_rooms', [
            'id' => $guchiRoom->id,
            'user_id' => $user->id,
            'title' => $guchiRoom->title,
            'genre' => $guchiRoom->genre,
        ]);

        $this->postJson('/api/guchiroom/delete/' . $guchiRoom->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/guchiroom/delete/' . $guchiRoom->id)
            ->assertOk();

        $this->assertDatabaseMissing('guchi_rooms', ['id' => $guchiRoom->id]);
    }


    // グチ部屋のブックマーク
    public function testBookmark()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $this->postJson('/api/guchiroom/bookmark/' . $guchiRoom->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/guchiroom/bookmark/' . $guchiRoom->id)
            ->assertOk();

        $this->assertDatabaseHas('guchi_bookmarks', [
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
        ]);
    }


    // グチ部屋のブックマークのキャンセル
    public function testUnBookmark()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $guchiBookmark = GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
        ]);

        $this->assertDatabaseHas('guchi_bookmarks', ['id' => $guchiBookmark->id]);

        $this->postJson('/api/guchiroom/unbookmark/' . $guchiRoom->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/guchiroom/unbookmark/' . $guchiRoom->id)
            ->assertOk();

        $this->assertDatabaseMissing('guchi_bookmarks', ['id' => $guchiBookmark->id]);
    }


    // グチ部屋全部を新着順で取得
    public function testGetRoomsNew()
    {
        $user = factory(User::class)->create();

        $guchiRoom1 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom2 = factory(GuchiRoom::class)->create(['genre' => 'life']);
        $guchiRoom3 = factory(GuchiRoom::class)->create(['genre' => 'game']);

        factory(Guchi::class, 5)->create(['guchi_room_id' => $guchiRoom1->id]);
        factory(Guchi::class, 4)->create(['guchi_room_id' => $guchiRoom2->id]);
        factory(Guchi::class, 3)->create(['guchi_room_id' => $guchiRoom3->id]);
        
        GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom2->id,
        ]);

        $this->getJson('/api/guchiroom/all/new?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchiroom/all/new?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiRoom3->title, '"guchis_count":3', '"bookmarked":false',
                $guchiRoom2->title, '"guchis_count":4', '"bookmarked":true',
                $guchiRoom1->title, '"guchis_count":5', '"bookmarked":false',
            ]);
    }


    // グチ部屋全部をグチが多い順で取得
    public function testGetRoomNew()
    {
        $user = factory(User::class)->create();

        $guchiRoom1 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom2 = factory(GuchiRoom::class)->create(['genre' => 'life']);
        $guchiRoom3 = factory(GuchiRoom::class)->create(['genre' => 'game']);

        factory(Guchi::class, 3)->create(['guchi_room_id' => $guchiRoom1->id]);
        factory(Guchi::class, 5)->create(['guchi_room_id' => $guchiRoom2->id]);
        factory(Guchi::class, 4)->create(['guchi_room_id' => $guchiRoom3->id]);
        
        GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom2->id,
        ]);

        $this->getJson('/api/guchiroom/all/trend?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchiroom/all/trend?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiRoom2->title, '"guchis_count":5', '"bookmarked":true',
                $guchiRoom3->title, '"guchis_count":4', '"bookmarked":false',
                $guchiRoom1->title, '"guchis_count":3', '"bookmarked":false',
            ]);
    }


    // グチ部屋をジャンル別に新着順で取得
    public function testGetRoomsGenreNew()
    {
        $user = factory(User::class)->create();

        $guchiRoom1 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom2 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom3 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom4 = factory(GuchiRoom::class)->create(['genre' => 'life']);

        factory(Guchi::class, 5)->create(['guchi_room_id' => $guchiRoom1->id]);
        factory(Guchi::class, 4)->create(['guchi_room_id' => $guchiRoom2->id]);
        factory(Guchi::class, 3)->create(['guchi_room_id' => $guchiRoom3->id]);

        GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom2->id,
        ]);

        $this->getJson('/api/guchiroom/genre/new/jobs?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchiroom/genre/new/jobs?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiRoom3->title, '"guchis_count":3', '"bookmarked":false',
                $guchiRoom2->title, '"guchis_count":4', '"bookmarked":true',
                $guchiRoom1->title, '"guchis_count":5', '"bookmarked":false',
            ])
            ->assertDontSee($guchiRoom4->title)
            ->assertJsonFragment(['total' => 3]);
    }


    // グチ部屋をジャンル別に人気順で取得
    public function testGetRoomsGenreTrend()
    {
        $user = factory(User::class)->create();

        $guchiRoom1 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom2 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom3 = factory(GuchiRoom::class)->create(['genre' => 'jobs']);
        $guchiRoom4 = factory(GuchiRoom::class)->create(['genre' => 'life']);

        factory(Guchi::class, 3)->create(['guchi_room_id' => $guchiRoom1->id]);
        factory(Guchi::class, 5)->create(['guchi_room_id' => $guchiRoom2->id]);
        factory(Guchi::class, 4)->create(['guchi_room_id' => $guchiRoom3->id]);
        factory(Guchi::class, 2)->create(['guchi_room_id' => $guchiRoom4->id]);
        
        GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom2->id,
        ]);

        $this->getJson('/api/guchiroom/genre/trend/jobs?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchiroom/genre/trend/jobs?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiRoom2->title, '"guchis_count":5', '"bookmarked":true',
                $guchiRoom3->title, '"guchis_count":4', '"bookmarked":false',
                $guchiRoom1->title, '"guchis_count":3', '"bookmarked":false',
            ])
            ->assertDontSee($guchiRoom4->title)
            ->assertJsonFragment(['total' => 3]);
    }


    // 検索したグチ部屋を新着順で取得
    public function testGetSearchGuchiroomNew()
    {
        $user = factory(User::class)->create();

        $guchiRoom1 = factory(GuchiRoom::class)->create([
            'title' => 'banana,apple',
            'genre' => 'jobs',
        ]);
        $guchiRoom2 = factory(GuchiRoom::class)->create([
            'title' => 'banana,dog',
            'genre' => 'jobs',
        ]);
        $guchiRoom3 = factory(GuchiRoom::class)->create([
            'title' => 'apple,dog',
            'genre' => 'jobs',
        ]);

        factory(Guchi::class, 5)->create(['guchi_room_id' => $guchiRoom1->id]);
        factory(Guchi::class, 3)->create(['guchi_room_id' => $guchiRoom2->id]);
        factory(Guchi::class, 4)->create(['guchi_room_id' => $guchiRoom3->id]);
        
        GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom2->id,
        ]);

        $this->getJson('/api/search/guchiroom/new/banana?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/search/guchiroom/new/banana?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiRoom2->title, '"guchis_count":3', '"bookmarked":true',
                $guchiRoom1->title, '"guchis_count":5', '"bookmarked":false',
            ])
            ->assertDontSee($guchiRoom3->title)
            ->assertJsonFragment(['total' => 2]);
    }


    // 検索したグチ部屋を人気順で取得
    public function testGetSearchGuchiroomPopular()
    {
        $user = factory(User::class)->create();

        $guchiRoom1 = factory(GuchiRoom::class)->create([
            'title' => 'banana,apple',
            'genre' => 'jobs',
        ]);
        $guchiRoom2 = factory(GuchiRoom::class)->create([
            'title' => 'banana,dog',
            'genre' => 'jobs',
        ]);
        $guchiRoom3 = factory(GuchiRoom::class)->create([
            'title' => 'apple,dog',
            'genre' => 'jobs',
        ]);

        factory(Guchi::class, 5)->create(['guchi_room_id' => $guchiRoom1->id]);
        factory(Guchi::class, 3)->create(['guchi_room_id' => $guchiRoom2->id]);
        factory(Guchi::class, 4)->create(['guchi_room_id' => $guchiRoom3->id]);
        
        GuchiBookmark::create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom2->id,
        ]);

        $this->getJson('/api/search/guchiroom/popular/banana?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/search/guchiroom/popular/banana?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiRoom1->title, '"guchis_count":5', '"bookmarked":false',
                $guchiRoom2->title, '"guchis_count":3', '"bookmarked":true',
            ])
            ->assertDontSee($guchiRoom3->title)
            ->assertJsonFragment(['total' => 2]);
    }


    // ユーザーがブックマークしたグチ部屋をブックマークした日付が新しい順で取得（５件ずつページネーション）
    public function testGetUserGuchiroom()
    {
        $user = factory(User::class)->create();

        $guchiBookmark1 = factory(GuchiBookmark::class)->create(['user_id' => $user->id]);
        $guchiBookmark2 = factory(GuchiBookmark::class)->create(['user_id' => $user->id]);
        $guchiBookmark3 = factory(GuchiBookmark::class)->create(['user_id' => $user->id]);
        $guchiBookmark4 = factory(GuchiBookmark::class)->create();

        $this->getJson('/api/guchiroom/user/' . $user->id . '?page=1')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchiroom/user/' . $user->id . '?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $guchiBookmark3->guchiRoom->title,
                $guchiBookmark2->guchiRoom->title,
                $guchiBookmark1->guchiRoom->title,
            ])
            ->assertDontSee($guchiBookmark4->guchiRoom->title);
    }


    // グチ詳細ページ（各スレ）用に認証ユーザー情報とグチ部屋情報を取得する
    public function testInit()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $this->getJson('/api/guchi/init/' . $guchiRoom->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchi/init/' . $guchiRoom->id)
            ->assertok()
            ->assertSee(
                $user->name,
                $guchiRoom->title,
            );
    }


    // グチの投稿
    public function testGuchiCreate()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $form = [
            'roomId' => $guchiRoom->id,
            'body' => 'aaaaaaaaaaaaaaa',
        ];
        $form2 = [
            'anonymous' => 'true',
            'roomId' => $guchiRoom->id,
            'body' => 'bbbbbbbbbbbbbbb',
        ];
        $form3 = [
            'roomId' => $guchiRoom->id,
            'body' => '',
        ];

        $this->postJson('/api/guchi/create', $form)
            ->assertStatus(401);

        // 匿名の場合の成功パターン
        $this->actingAs($user)
            ->postJson('/api/guchi/create', $form)
            ->assertOk();
        $this->assertDatabaseHas('guchis', [
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
            'body' => $form['body'],
        ]);

        // ユーザー名公開の場合の成功パターン
        $this->postJson('/api/guchi/create', $form2)
            ->assertOk();
        $this->assertDatabaseHas('guchis', [
            'user_id' => null,
            'guchi_room_id' => $guchiRoom->id,
            'body' => $form2['body'],
        ]);

        // body未入力によるバリデーションエラー
        $this->postJson('/api/guchi/create', $form3)
            ->assertStatus(422);
    }


    // グチの削除
    public function testGuchiDelete()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $guchi = factory(Guchi::class)->create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
        ]);

        $this->assertDatabaseHas('guchis', [
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
            'body' => $guchi->body,
        ]);

        $this->postJson('/api/guchi/delete/' . $guchi->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/guchi/delete/' . $guchi->id)
            ->assertOk();

        $this->assertDatabaseMissing('guchis', [
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
            'body' => $guchi->body,
        ]);
    }


    // グチにいいね
    public function testGood()
    {
        $user = factory(User::class)->create();

        $guchi = factory(Guchi::class)->create();

        $this->postJson('/api/guchi/good/' . $guchi->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/guchi/good/' . $guchi->id)
            ->assertOk();

        $this->assertDatabaseHas('guchi_goods', [
            'user_id' => $user->id,
            'guchi_id' => $guchi->id,
        ]);
    }


    // グチへのいいねのキャンセル
    public function testUngood()
    {
        $user = factory(User::class)->create();

        $guchi = factory(Guchi::class)->create();

        GuchiGood::create([
            'user_id' => $user->id,
            'guchi_id' => $guchi->id,
        ]);

        $this->assertDatabaseHas('guchi_goods', [
            'user_id' => $user->id,
            'guchi_id' => $guchi->id,
        ]);

        $this->postJson('/api/guchi/ungood/' . $guchi->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/guchi/ungood/' . $guchi->id)
            ->assertOk();

        $this->assertDatabaseMissing('guchi_goods', [
            'user_id' => $user->id,
            'guchi_id' => $guchi->id,
        ]);
    }


    // 最新のグチを１件取得
    public function testGetLatestGuchi()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $guchi1 = factory(Guchi::class)->create(['guchi_room_id' => $guchiRoom->id]);
        $guchi2 = factory(Guchi::class)->create(['guchi_room_id' => $guchiRoom->id]);
        $guchi3 = factory(Guchi::class)->create([
            'user_id' => $user->id,
            'guchi_room_id' => $guchiRoom->id,
        ]);

        $this->getJson('/api/guchi/latest/' . $guchiRoom->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchi/latest/' . $guchiRoom->id)
            ->assertOk()
            ->assertjsonFragment([
                'id' => $guchi3->id,
                'body' => $guchi3->body,
                'isSelf' => true,
            ]);
    }


    // グチ部屋のグチ（発言）の最新５件を古い順で取得（５件ずつ無限スクロール）
    public function testGuchiGet()
    {
        $user = factory(User::class)->create();

        $guchiRoom = factory(GuchiRoom::class)->create();

        $guchi1 = factory(Guchi::class)->create(['guchi_room_id' => $guchiRoom->id, 'user_id' => $user->id]);
        $guchi2 = factory(Guchi::class)->create(['guchi_room_id' => $guchiRoom->id]);
        $guchi3 = factory(Guchi::class)->create(['guchi_room_id' => $guchiRoom->id]);
        $guchi4 = factory(Guchi::class)->create(['guchi_room_id' => $guchiRoom->id]);
        $guchi5 = factory(Guchi::class)->create();

        factory(GuchiImage::class, 2)->create(['guchi_id' => $guchi2->id]);
        factory(GuchiImage::class, 1)->create(['guchi_id' => $guchi4->id]);

        GuchiGood::create([
            'user_id' => $user->id,
            'guchi_id' => $guchi3->id,
        ]);

        $this->getJson('/api/guchi/get/' . $guchiRoom->id . '?loaded_guchis_count=0')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/guchi/get/' . $guchiRoom->id)
            ->assertOk()
            ->assertSeeInOrder([
                $guchi1->body, '"gooded":false', '"isSelf":true',
                $guchi2->body, '"gooded":false', '"isSelf":false',
                $guchi3->body, '"gooded":true', '"isSelf":false',
                $guchi4->body, '"gooded":false', '"isSelf":false',
            ])
            ->assertDontSee($guchi5->body)
            ->assertJsonFragment([
                'imagesCount' => 3,
                'guchisTotal' => 4,
            ]);
    }
}
