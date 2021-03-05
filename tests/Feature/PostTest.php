<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;

use App\User;
use App\Follow;
use App\Post;
use App\PostImage;
use App\Tag;
use App\PostTag;
use App\Donmai;
use App\Reply;


class PostTest extends TestCase
{
    use RefreshDatabase;
    
    // 認証ユーザー情報と全ジャンルを取得
    public function testGetAuthAndGenre()
    {
        $user = factory(User::class)->create();

        $this->getJson('/api/authandgenre')->assertStatus(401);
        $this->actingAs($user)
            ->getJson('/api/authandgenre')
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
            ]);
    }


    // 全ジャンルの取得
    public function testGetGenres()
    {
        $user = factory(User::class)->create();

        $this->getJson('/api/genre')->assertStatus(401);
        $this->actingAs($user)
            ->getJson('/api/genre')
            ->assertStatus(200)
            ->assertSeeInOrder(['jobs', 'shame', 'game']);
    }


    // 投稿
    public function testCreate()
    {
        $user = factory(User::class)->create();

        // 成功パターン
        $form = [
            'user_id' => $user->id,
            'body' => 'こんにちは。さようなら。また明日。',
            'genreIndex' => 1,
            'tags' => '#ああ #いい　#うう ええ',
        ];
        $this->postJson('/api/post/add', $form)->assertStatus(401);
        $this->actingAs($user)
            ->postJson('/api/post/add', $form)
            ->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'body' => $form['body'],
            'genre_index' => 1,
        ]);
        $post = Post::where('body', $form['body'])->first();
        $this->assertDatabaseHas('tags', ['name' => 'ああ']);
        $this->assertDatabaseHas('tags', ['name' => 'いい']);
        $this->assertDatabaseHas('tags', ['name' => 'うう']);
        $this->assertDatabaseMissing('tags', ['name' => 'ええ']);  // ハッシュタグから始まらない単語はレコード作成されていないことをテスト
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id]);

        // genreIndex未入力によるバリデーションエラー
        $form['genreIndex'] = null;
        $this->actingAs($user)
            ->postJson('/api/post/add', $form)
            ->assertStatus(422);
    }


    // 投稿の編集
    public function testEdit()
    {
        $post = factory(Post::class)->create();

        // 投稿に紐つくタグを作成
        $tag1 = Tag::create([
            'name' => 'りんご',
        ]);
        $tag2 = Tag::create([
            'name' => 'バナナ',
        ]);
        // 上のタグと投稿を紐つける中間テーブル（post_tag）のレコード作成
        $post->tags()->attach([$tag1->id, $tag2->id]);

        $user = User::find($post->user_id);

        $editForm = [
            'id' => $post->id,
            'body' => 'ヤッホー！ヤッホー！',
            'genreIndex' => 2,
            'tags' => '#りんご #キウイ',
        ];
        $this->postJson('/api/post/edit', $editForm)
            ->assertStatus(401);
        $this->actingAs($user)
            ->postJson('/api/post/edit', $editForm)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $editForm['id'],
                'body' => $editForm['body'],
                'genre_index' => 2,
            ]);
        $this->assertDatabaseHas('posts', [
            'body' => $editForm['body'],
            'genre_index' => 2,
        ]);
        $this->assertDatabaseHas('tags', ['name' => 'りんご']);
        $this->assertDatabaseHas('tags', ['name' => 'キウイ']);
        $this->assertDatabaseMissing('tags', ['name' => 'バナナ']);  // 紐着く投稿が０になったタグは削除されているかをテスト
        // 中間テーブルの削除を確認
        $this->assertDatabaseMissing('post_tag', [
                                        'post_id' => $post->id,
                                        'tag_id' => $tag2->id,
                                    ]);
    }


    // 投稿の削除
    public function testDelete()
    {
        $post = factory(Post::class)->create();

        $tag1 = Tag::create([
            'name' => 'カレー',
        ]);
        $tag2 = Tag::create([
            'name' => 'オムライス',
        ]);
        $post->tags()->attach([$tag1->id, $tag2->id]);

        $user = User::find($post->user_id);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertDatabaseHas('tags', ['name' => 'カレー']);
        $this->assertDatabaseHas('tags', ['name' => 'オムライス']);
        $this->assertDatabaseHas('post_tag', [
                                    'post_id' => $post->id,
                                    'tag_id' => $tag1->id,
                                ]);
        $this->assertDatabaseHas('post_tag', [
                                    'post_id' => $post->id,
                                    'tag_id' => $tag2->id,
                                ]);

        $this->postJson('/api/post/delete/' . $post->id)
            ->assertStatus(401);
        $this->actingAs($user)
            ->postJson('/api/post/delete/' . $post->id)
            ->assertStatus(200);
        
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('post_tag', [
                                    'post_id' => $post->id,
                                    'tag_id' => $tag1->id,
                                ]);
        $this->assertDatabaseMissing('post_tag', [
                                    'post_id' => $post->id,
                                    'tag_id' => $tag2->id,
                                ]);
        // 投稿を削除した際、どの投稿にも紐つけられないタグが発生する場合はそのタグが削除されているかテスト
        $this->assertDatabaseMissing('tags', ['name' => 'カレー']);
        $this->assertDatabaseMissing('tags', ['name' => 'オムライス']);
    }


    // フォロー中のユーザーの投稿優先で、投稿を新着順で全て取得（３件ずつ無限スクロール）
    public function testGetPosts()
    {
        $authUser = factory(User::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        Follow::create([
            'user_id' => $authUser->id,
            'following_user_id' => $user1->id,
        ]);
        
        $post1 = factory(Post::class)->create([
            'user_id' => $user2->id,
        ]);
        $post2 = factory(Post::class)->create([
            'user_id' => $user1->id,
        ]);
        $post3 = factory(Post::class)->create([
            'user_id' => $user2->id,
        ]);
        $post4 = factory(Post::class)->create([
            'user_id' => $authUser->id,
        ]);
        $post5 = factory(Post::class)->create([
            'user_id' => $user1->id,
        ]);

        $this->getJson('/api/post/get?loaded_posts_count=0')
            ->assertStatus(401);

        $this->actingAs($authUser)
            ->getJson('/api/post/get?loaded_posts_count=0')
            ->assertStatus(200)
            ->assertSeeInOrder([$post5->body, $post4->body, $post2->body])  // 先にフォロー中のユーザーの投稿を送信しているかテスト
            ->assertJsonFragment(['postsTotal' => 5]);

        $this->getJson('/api/post/get?loaded_posts_count=3')
            ->assertStatus(200)
            ->assertSeeInOrder([$post3->body, $post1->body])  // 後でフォローしていないユーザーの投稿を送信しているはず
            ->assertJsonFragment(['postsTotal' => 5]);
    }


    // ジャンル別で投稿を取得（３件ずつ無限スクロール）
    public function testGetGenrePosts()
    {
        $user = factory(User::class)->create();
        
        $post1 = factory(Post::class)->create([
            'user_id' => $user->id,
            'genre_index' => 1,
        ]);
        $post2 = factory(Post::class)->create([
            'user_id' => $user->id,
            'genre_index' => 1,
        ]);
        $post3 = factory(Post::class)->create([
            'user_id' => $user->id,
            'genre_index' => 1,
        ]);
        $post4 = factory(Post::class)->create([
            'user_id' => $user->id,
            'genre_index' => 1,
        ]);
        $post5 = factory(Post::class)->create([
            'user_id' => $user->id,
            'genre_index' => 2,
        ]);
        $post6 = factory(Post::class)->create([
            'user_id' => $user->id,
            'genre_index' => 3,
        ]);

        $this->getJson('/api/post/genre/get/life?loaded_posts_count=0')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/post/genre/get/life?loaded_posts_count=0')
            ->assertStatus(200)
            ->assertSeeInOrder([$post4->body, $post3->body, $post2->body])
            ->assertDontSee($post5->body, $post6->body)
            ->assertJsonFragment(['postsTotal' => 4]);

        $this->getJson('/api/post/genre/get/life?loaded_posts_count=3')
            ->assertSee($post1->body)
            ->assertDontSee($post5->body, $post6->body)
            ->assertJsonFragment(['postsTotal' => 4]);
    }


    // 話題の投稿ページで投稿を取得（ここ１週間以内）
    public function testGetHotposts()
    {
        $user = factory(User::class)->create();

        $date1 = new Carbon('-10 day', 'Asia/Tokyo');
        $post1 = factory(Post::class)->create([
            'user_id' => $user->id,
            'created_at' => $date1,
        ]);
        $date2 = new Carbon('-9 day', 'Asia/Tokyo');
        $post2 = factory(Post::class)->create([
            'user_id' => $user->id,
            'created_at' => $date2,
        ]);
        $date3 = new Carbon('-6 day', 'Asia/Tokyo');
        $post3 = factory(Post::class)->create([
            'user_id' => $user->id,
            'created_at' => $date3,
        ]);
        factory(Donmai::class, 5)->create([
            'post_id' => $post3->id,
        ]);
        $date4 = new Carbon('-5 day', 'Asia/Tokyo');
        $post4 = factory(Post::class)->create([
            'user_id' => $user->id,
            'created_at' => $date4,
        ]);
        factory(Donmai::class, 2)->create([
            'post_id' => $post4->id,
        ]);
        $date5 = new Carbon('-4 day', 'Asia/Tokyo');
        $post5 = factory(Post::class)->create([
            'user_id' => $user->id,
            'created_at' => $date5,
        ]);
        factory(Donmai::class, 4)->create([
            'post_id' => $post5->id,
        ]);
        $date6 = new Carbon('-3 day', 'Asia/Tokyo');
        $post6 = factory(Post::class)->create([
            'user_id' => $user->id,
            'created_at' => $date6,
        ]);
        factory(Donmai::class, 3)->create([
            'post_id' => $post6->id,
        ]);

        $this->getJson('/api/post/hot/get?loaded_posts_count=0')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/post/hot/get?loaded_posts_count=0')
            ->assertStatus(200)
            ->assertSeeInOrder([$post3->body, $post5->body, $post6->body])
            ->assertDontSee($post1->body, $post2->body, $post4->body)
            ->assertJsonFragment(['postsTotal' => 4]);

        $this->getJson('/api/post/hot/get?loaded_posts_count=3')
            ->assertStatus(200)
            ->assertSee($post4->body)
            ->assertDontSee($post1->body, $post2->body, $post3->body, $post5->body, $post6->body)
            ->assertJsonFragment(['postsTotal' => 4]);
    }


    // ユーザーページでユーザーの投稿を新着順で取得（３件ずつ無限スクロール）
    public function testGetUserPostsOnly()
    {
        $user = factory(User::class)->create();

        $post1 = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);
        $post2 = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);
        $post3 = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);
        $post4 = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);
        $post5 = factory(Post::class)->create([
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/post/user/get/' . $user->id . '?loaded_posts_count=0')
            ->assertStatus(401);
        
        // ０スクロールでのレスポンス
        $this->actingAs($user)
            ->getJson('/api/post/user/get/' . $user->id . '?loaded_posts_count=0')
            ->assertOK()
            ->assertSeeInOrder([$post5->body, $post4->body, $post3->body])
            ->assertDontSee($post2->body, $post1->body)
            ->assertJsonFragment(['postsTotal' => 5]);

        // １スクロールでのレスポンス
        $this->actingAs($user)
            ->getJson('/api/post/user/get/' . $user->id . '?loaded_posts_count=3')
            ->assertOK()
            ->assertSeeInOrder([$post2->body, $post1->body])
            ->assertDontSee($post5->body, $post4->body, $post3->body)
            ->assertJsonFragment(['postsTotal' => 5]);
    }


    // ユーザーがどんまいした投稿を取得（３件ずつ無限スクロール）
    public function testGetUserDonmaiPostsOnly()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $post1 = factory(Post::class)->create();
        factory(Donmai::class)->create([
            'user_id' => $user->id,
            'post_id' => $post1->id,
        ]);
        $post2 = factory(Post::class)->create();
        factory(Donmai::class)->create([
            'user_id' => $user->id,
            'post_id' => $post2->id,
        ]);
        $post3 = factory(Post::class)->create();
        factory(Donmai::class)->create([
            'user_id' => $user->id,
            'post_id' => $post3->id,
        ]);
        $post4 = factory(Post::class)->create();
        factory(Donmai::class)->create([
            'user_id' => $user->id,
            'post_id' => $post4->id,
        ]);
        $post5 = factory(Post::class)->create();
        factory(Donmai::class)->create([
            'user_id' => $user2->id,
            'post_id' => $post5->id,
        ]);
        
        $this->getJson('/api/post/user/donmai/' . $user->id . '?loaded_posts_count=0')
            ->assertStatus(401);

        // ０スクロールでのレスポンス
        $this->actingAs($user)
            ->getJson('/api/post/user/donmai/' . $user->id . '?loaded_posts_count=0')
            ->assertStatus(200)
            ->assertDontSee($post5->body, $post1->body)
            ->assertSeeInOrder([$post4->body, $post3->body, $post2->body])
            ->assertJsonFragment(['postsTotal' => 4]);

        // １スクロールでのレスポンス
        $this->actingAs($user)
            ->getJson('/api/post/user/donmai/' . $user->id . '?loaded_posts_count=1')
            ->assertStatus(200)
            ->assertDontSee($post5->body, $post4->body, $post3->body, $post2->body)
            ->assertSee($post1->body)
            ->assertJsonFragment(['postsTotal' => 4]);
    }


    // 検索したワードをタグに含む投稿を新着順で取得（３件ずつ無限スクロール）
    public function testGetSearchNewPostsOnly()
    {
        $user = factory(User::class)->create();

        $post1 = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();
        $post3 = factory(Post::class)->create();
        $post4 = factory(Post::class)->create();
        $post5 = factory(Post::class)->create();
        $post6 = factory(Post::class)->create();

        $tag1 = Tag::create(['name' => 'いちご']);
        $tag2 = Tag::create(['name' => 'みかん']);

        $tag1->posts()->attach([$post1->id, $post2->id, $post3->id, $post4->id]);
        $tag2->posts()->attach([$post5->id, $post6->id]);

        $this->getJson('/api/post/search/new/' . urlencode('いちご') . '?loaded_posts_count=0')
            ->assertStatus(401);

        // ０スクロールでのレスポンス（検索ワードはエンコードしてテスト）
        $this->actingAs($user)
            ->getJson('/api/post/search/new/' . urlencode('いちご') . '?loaded_posts_count=0')
            ->assertStatus(200)
            ->assertDontSee($post5->body, $post6->body, $post4->body)
            ->assertSeeInOrder([$post4->body, $post3->body, $post2->body])
            ->assertJsonFragment(['postsTotal' => 4]);

        // １スクロールでのレスポンス（検索ワードはエンコードしてテスト）
        $this->actingAs($user)
            ->getJson('/api/post/search/new/' . urlencode('いちご') . '?loaded_posts_count=3')
            ->assertStatus(200)
            ->assertDontSee($post5->body, $post6->body, $post4->body, $post3->body, $post2->body)
            ->assertSee($post1->body)
            ->assertJsonFragment(['postsTotal' => 4]);
    }


    // 検索したワードをタグに含む投稿をどんまい数が多い順で取得（３件ずつ無限スクロール）
    public function testGetSearchPopularPostsOnly()
    {
        $user = factory(User::class)->create();

        // どんまい数が多い順は「$post3->$post1->$post4->$post2」
        $post1 = factory(Post::class)->create();   // 'いちご'タグを紐つけ
        factory(Donmai::class, 3)->create([
            'post_id' => $post1->id,
        ]);
        $post2 = factory(Post::class)->create();   // 'いちご'タグを紐つけ
        factory(Donmai::class, 1)->create([
            'post_id' => $post2->id,
        ]);
        $post3 = factory(Post::class)->create();   // 'いちご'タグを紐つけ
        factory(Donmai::class, 4)->create([
            'post_id' => $post3->id,
        ]);
        $post4 = factory(Post::class)->create();   // 'いちご'タグを紐つけ
        factory(Donmai::class, 2)->create([
            'post_id' => $post4->id,
        ]);
        $post5 = factory(Post::class)->create();   // 'みかん'タグを紐つけ
        $post6 = factory(Post::class)->create();   // 'みかん'タグを紐つけ

        $tag1 = Tag::create(['name' => 'いちご']);
        $tag2 = Tag::create(['name' => 'みかん']);

        $tag1->posts()->attach([$post1->id, $post2->id, $post3->id, $post4->id]);
        $tag2->posts()->attach([$post5->id, $post6->id]);

        $this->getJson('/api/post/search/popular/' . urlencode('いちご') . '?loaded_posts_count=0')
            ->assertStatus(401);

        // ０スクロールでのレスポンス（検索ワードはエンコードしてテスト）
        $this->actingAs($user)
            ->getJson('/api/post/search/popular/' . urlencode('いちご') . '?loaded_posts_count=0')
            ->assertStatus(200)
            ->assertDontSee($post5->body, $post6->body, $post2->body)
            ->assertSeeInOrder([$post3->body, $post1->body, $post4->body])
            ->assertJsonFragment(['postsTotal' => 4]);

        // １スクロールでのレスポンス（検索ワードはエンコードしてテスト）
        $this->getJson('/api/post/search/popular/' . urlencode('いちご') . '?loaded_posts_count=3')
            ->assertStatus(200)
            ->assertDontSee($post5->body, $post6->body, $post3->body, $post1->body, $post4->body)
            ->assertSee($post2->body)
            ->assertJsonFragment(['postsTotal' => 4]);
    }
}
