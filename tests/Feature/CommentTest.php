<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Post;
use App\Comment;
use App\CommentGood;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // 投稿のコメントを最新順で取得（５件ずつ無限スクロール）
    public function testGetComments()
    {
        $authUser = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();

        $comment1 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment2 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment3 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment4 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment5 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment6 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment7 = factory(Comment::class)->create([
            'post_id' => $post->id,
        ]);
        $comment8 = factory(Comment::class)->create([
            'post_id' => $post2->id,
        ]);

        CommentGood::create(['user_id' => $authUser->id, 'comment_id' => $comment2->id]);
        CommentGood::create(['user_id' => $authUser->id, 'comment_id' => $comment5->id]);
        CommentGood::create(['user_id' => $authUser->id, 'comment_id' => $comment7->id]);

        $this->getJson('/api/comments/get/' . $post->id . '?loaded_comments_count=0')
            ->assertStatus(401);

        // ０スクロールでのレスポンス
        $this->actingAs($authUser)
            ->getJson('/api/comments/get/' . $post->id . '?loaded_comments_count=0')
            ->assertOk()
            ->assertSeeInOrder([
                $comment7->body, '"gooded":true',
                $comment6->body, '"gooded":false',
                $comment5->body, '"gooded":true',
                $comment4->body, '"gooded":false',
                $comment3->body, '"gooded":false',
            ])
            ->assertDontSee(
                $comment1->body,
                $comment2->body,
                $comment8->body
            )
            ->assertJsonFragment(['commentsTotal' => 7]);

        // １スクロールでのレスポンス
        $this->getJson('/api/comments/get/' . $post->id . '?loaded_comments_count=5')
            ->assertOk()
            ->assertSeeInOrder([
                $comment2->body, '"gooded":true',
                $comment1->body, '"gooded":false',
            ])
            ->assertDontSee(
                $comment8->body,
                $comment7->body, 
                $comment6->body,
                $comment5->body,
                $comment4->body,
                $comment3->body,
            )
            ->assertJsonFragment(['commentsTotal' => 7]);
    }


    // 投稿に対するコメントの投稿
    public function testCreate()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        $form = [
            'postId' => $post->id,
            'body' => 'あははそれはどんまい！',
        ];
        $form2 = [
            'postId' => $post->id,
            'body' => '',
        ];

        $this->postJson('/api/comment', $form)
            ->assertStatus(401);

        $this->actingAs($user);

        $this->postJson('/api/comment', $form2)
            ->assertStatus(422);

        $this->postJson('/api/comment', $form)
            ->assertStatus(200)
            ->assertJsonFragment([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'body' => $form['body'],
            ]);
        
        $this->assertDatabaseHas('comments', [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'body' => $form['body'],
            ]);
    }


    // コメントの削除
    public function testDelete()
    {
        $user = factory(User::class)->create();

        $comment = factory(Comment::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('comments', [
                'id' => $comment->id,
            ]);

        $this->postJson('/api/comment/delete/' . $comment->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/comment/delete/' . $comment->id)
            ->assertStatus(200);
        
        $this->assertDatabaseMissing('comments', [
                'id' => $comment->id,
            ]);
    }


    // コメントへのいいね処理
    public function testGood()
    {
        $user = factory(User::class)->create();

        $comment = factory(Comment::class)->create();

        $this->postJson('/api/comment/good/' . $comment->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/comment/good/' . $comment->id)
            ->assertOk();

        $this->assertDatabaseHas('comment_goods', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
        ]);
    }


    // コメントへのいいねのキャンセル
    public function testUnGood()
    {
        $user = factory(User::class)->create();

        $comment = factory(Comment::class)->create();

        $commentGood = CommentGood::create([
            'user_id' => $user->id,
            'comment_id' => $comment->id,
        ]);

        $this->assertDatabaseHas('comment_goods', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
        ]);

        $this->postJson('/api/comment/ungood/' . $comment->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/comment/ungood/' . $comment->id)
            ->assertOk();
        
        $this->assertDatabaseMissing('comment_goods', [
            'id' => $commentGood->id,
        ]);
    }
}
