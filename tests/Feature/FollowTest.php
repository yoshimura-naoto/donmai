<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Follow;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    // フォロー処理
    public function testFollow()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->postJson('/api/follow', ['id' => $user2->id])
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/follow', ['id' => $user2->id])
            ->assertStatus(200);

        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'following_user_id' => $user2->id,
        ]);
    }


    // アンフォロー処理
    public function testUnfollow()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        
        Follow::create([
            'user_id' => $user->id,
            'following_user_id' => $user2->id,
        ]);

        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'following_user_id' => $user2->id,
        ]);

        $this->postJson('/api/unfollow', ['id' => $user2->id])
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/unfollow', ['id' => $user2->id])
            ->assertStatus(200);

        $this->assertDatabaseMissing('follows', [
            'user_id' => $user->id,
            'following_user_id' => $user2->id,
        ]);
    }


    // あるユーザーのフォロー中の人たちを返す（８件ずつ無限スクロール）
    public function testFollowingShow()
    {
        $authUser = factory(User::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        $user5 = factory(User::class)->create();
        $user6 = factory(User::class)->create();
        $user7 = factory(User::class)->create();
        $user8 = factory(User::class)->create();
        $user9 = factory(User::class)->create();
        $user10 = factory(User::class)->create();
        $user11 = factory(User::class)->create();
        $user12 = factory(User::class)->create();
        
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user2->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user3->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user4->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user5->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user6->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user7->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user8->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user9->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user10->id]);
        Follow::create(['user_id' => $user1->id, 'following_user_id' => $user11->id]);

        Follow::create(['user_id' => $authUser->id, 'following_user_id' => $user3->id]);
        Follow::create(['user_id' => $authUser->id, 'following_user_id' => $user6->id]);
        Follow::create(['user_id' => $authUser->id, 'following_user_id' => $user11->id]);
        
        $this->getJson('/api/following/' . $user1->id . '?page=1')
            ->assertStatus(401);

        // ０スクロールでのレスポンス
        $this->actingAs($authUser)
            ->getJson('/api/following/' . $user1->id . '?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $user11->name, '"followed":true',
                $user10->name, '"followed":false',
                $user9->name, '"followed":false',
                $user8->name, '"followed":false',
                $user7->name, '"followed":false',
                $user6->name, '"followed":true',
                $user5->name, '"followed":false',
                $user4->name, '"followed":false',
            ])
            ->assertDontSee(
                $authUser->name,
                $user1->name,
                $user2->name,
                $user3->name,
                $user12->name)
            ->assertJsonFragment(['total' => 10]);

        // 1スクロールでのレスポンス
        $this->actingAs($authUser)
            ->getJson('/api/following/' . $user1->id . '?page=2')
            ->assertOk()
            ->assertSeeInOrder([
                $user3->name, '"followed":true',
                $user2->name, '"followed":false',
            ])
            ->assertDontSee(
                $authUser->name,
                $user1->name, 
                $user5->name, 
                $user7->name,
                $user9->name)
            ->assertJsonFragment(['total' => 10]);
    }


    // あるユーザーのフォロワーたちを返す（８件ずつ無限スクロール）
    public function testFollowerShow()
    {
        $authUser = factory(User::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        $user5 = factory(User::class)->create();
        $user6 = factory(User::class)->create();
        $user7 = factory(User::class)->create();
        $user8 = factory(User::class)->create();
        $user9 = factory(User::class)->create();
        $user10 = factory(User::class)->create();
        $user11 = factory(User::class)->create();
        $user12 = factory(User::class)->create();
        
        Follow::create(['user_id' => $user2->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user3->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user4->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user5->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user6->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user7->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user8->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user9->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user10->id, 'following_user_id' => $user1->id]);
        Follow::create(['user_id' => $user11->id, 'following_user_id' => $user1->id]);

        Follow::create(['user_id' => $authUser->id, 'following_user_id' => $user3->id]);
        Follow::create(['user_id' => $authUser->id, 'following_user_id' => $user6->id]);
        Follow::create(['user_id' => $authUser->id, 'following_user_id' => $user11->id]);
        
        $this->getJson('/api/follower/' . $user1->id . '?page=1')
            ->assertStatus(401);

        // ０スクロールでのレスポンス
        $this->actingAs($authUser)
            ->getJson('/api/follower/' . $user1->id . '?page=1')
            ->assertOk()
            ->assertSeeInOrder([
                $user11->name, '"followed":true',
                $user10->name, '"followed":false',
                $user9->name, '"followed":false',
                $user8->name, '"followed":false',
                $user7->name, '"followed":false',
                $user6->name, '"followed":true',
                $user5->name, '"followed":false',
                $user4->name, '"followed":false',
            ])
            ->assertDontSee(
                $authUser->name, 
                $user1->name, 
                $user2->name, 
                $user3->name, 
                $user12->name
            )
            ->assertJsonFragment(['total' => 10]);

        // 1スクロールでのレスポンス
        $this->actingAs($authUser)
            ->getJson('/api/follower/' . $user1->id . '?page=2')
            ->assertOk()
            ->assertSeeInOrder([
                $user3->name, '"followed":true',
                $user2->name, '"followed":false',
            ])
            ->assertDontSee(
                $authUser->name,
                $user1->name,
                $user5->name,
                $user7->name,
                $user9->name,
                $user12->name)
            ->assertJsonFragment(['total' => 10]);
    }
}
