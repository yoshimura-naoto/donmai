<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Comment;
use App\Reply;
use App\ReplyGood;

class ReplyTest extends TestCase
{
    use RefreshDatabase;
    
    // コメントへの返信を古い順で取得（５件ずつ）
    public function testGet()
    {
        $user = factory(User::class)->create();

        $comment = factory(Comment::class)->create();
        $comment2 = factory(Comment::class)->create();

        $reply1 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply2 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply3 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply4 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply5 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply6 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply7 = factory(Reply::class)->create(['comment_id' => $comment->id]);
        $reply8 = factory(Reply::class)->create(['comment_id' => $comment2->id]);

        ReplyGood::create([
            'user_id' => $user->id,
            'reply_id' => $reply2->id,
        ]);
        ReplyGood::create([
            'user_id' => $user->id,
            'reply_id' => $reply4->id,
        ]);
        ReplyGood::create([
            'user_id' => $user->id,
            'reply_id' => $reply6->id,
        ]);

        $this->getJson('/api/replies/get/' . $comment->id . '?loaded_replies_count=0')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/replies/get/' . $comment->id . '?loaded_replies_count=0')
            ->assertOk()
            ->assertSeeInOrder([
                $reply1->body, '"gooded":false',
                $reply2->body, '"gooded":true',
                $reply3->body, '"gooded":false',
                $reply4->body, '"gooded":true',
                $reply5->body, '"gooded":false',
            ])
            ->assertDontSee(
                $reply6->body,
                $reply7->body,
                $reply8->body
            )
            ->assertJsonFragment(['repliesTotal' => 7]);
        
        $this->getJson('/api/replies/get/' . $comment->id . '?loaded_replies_count=5')
            ->assertOk()
            ->assertSeeInOrder([
                $reply6->body, '"gooded":true',
                $reply7->body, '"gooded":false',
            ])
            ->assertDontSee(
                $reply1->body,
                $reply2->body,
                $reply3->body,
                $reply4->body,
                $reply5->body,
                $reply8->body
            )
            ->assertJsonFragment(['repliesTotal' => 7]);
    }


    // コメントへの返信の投稿
    public function testCreate()
    {
        $user = factory(User::class)->create();

        $comment = factory(Comment::class)->create();

        $form = [
            'commentId' => $comment->id,
            'body' => 'どんまいです。',
        ];
        $form2 = [
            'commentId' => $comment->id,
            'body' => '',
        ];

        $this->postJson('/api/comment/reply', $form)
            ->assertStatus(401);

        $this->actingAs($user);

        $this->postJson('/api/comment/reply', $form2)
            ->assertStatus(422);

        $this->postJson('/api/comment/reply', $form)
            ->assertOk()
            ->assertJsonFragment([
                'user_id' => $user->id,
                'comment_id' => $comment->id,
                'body' => $form['body'],
            ]);

        $this->assertDatabaseHas('replies', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
            'body' => $form['body'],
        ]);
    }


    // コメントへの返信の削除
    public function testDelete()
    {
        $user = factory(User::class)->create();

        $reply = factory(Reply::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id]);

        $this->postJson('/api/reply/delete/' . $reply->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/reply/delete/' . $reply->id)
            ->assertOk();
        
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }


    // コメントへの返信へのいいね
    public function testGood()
    {
        $user = factory(User::class)->create();

        $reply = factory(Reply::class)->create();

        $this->postJson('/api/comment/reply/good/' . $reply->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/comment/reply/good/' . $reply->id)
            ->assertOk();

        $this->assertDatabaseHas('reply_goods', [
            'user_id' => $user->id,
            'reply_id' => $reply->id,
        ]);
    }


    // コメントへの返信へのいいねのキャンセル
    public function testUngood()
    {
        $user = factory(User::class)->create();

        $reply = factory(Reply::class)->create();

        ReplyGood::create([
            'user_id' => $user->id,
            'reply_id' => $reply->id,
        ]);

        $this->assertDatabaseHas('reply_goods', [
            'user_id' => $user->id,
            'reply_id' => $reply->id,
        ]);

        $this->postJson('/api/comment/reply/ungood/' . $reply->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/comment/reply/ungood/' . $reply->id)
            ->assertOk();

        $this->assertDatabaseMissing('reply_goods', [
            'user_id' => $user->id,
            'reply_id' => $reply->id,
        ]);
    }
}
