<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Post;
use App\Donmai;
use App\Follow;


class DonmaiTest extends TestCase
{
    use RefreshDatabase;

    // 投稿へのどんまい
    public function testDonmai()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        $this->postJson('/api/donmai/' . $post->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/donmai/' . $post->id)
            ->assertStatus(200);
        
        $this->assertDatabaseHas('donmais', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }


    // 投稿へのどんまいキャンセル処理
    public function testUnDonmai()
    {
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        Donmai::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertDatabaseHas('donmais', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->postJson('/api/undonmai/' . $post->id)
            ->assertStatus(401);

        $this->actingAs($user)
            ->postJson('/api/undonmai/' . $post->id)
            ->assertStatus(200);
        
        $this->assertDatabaseMissing('donmais', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }


    // その投稿にどんまいしたユーザーたちの取得
    public function testGetDonmaiUser()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        Follow::create([
            'user_id' => $user1->id,
            'following_user_id' => $user2->id,
        ]);

        $post = factory(Post::class)->create();

        $donmai1 = Donmai::create([
            'user_id' => $user1->id,
            'post_id' => $post->id,
        ]);
        $donmai2 = Donmai::create([
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);

        $lastDonmaiIdPlusOne = $donmai2->id + 1;

        $this->getJson('/api/donmai/users/' . $post->id . '?last_donmai_id=' . $lastDonmaiIdPlusOne)
            ->assertStatus(401);

        $this->actingAs($user1)
            ->getJson('/api/donmai/users/' . $post->id . '?last_donmai_id=' . $lastDonmaiIdPlusOne)
            ->assertStatus(200)
            ->assertSeeInOrder([
                $user2->name, '"followed":true',
                $user1->name, '"followed":false',
            ])
            ->assertDontSee($user3->name);
    }
}
