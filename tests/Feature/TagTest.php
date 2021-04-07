<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;

use App\User;
use App\Post;
use App\Donmai;
use App\Tag;
use App\PostTag;

class TagTest extends TestCase
{
    use RefreshDatabase;

    // タグのトレンドランキング（ここ１週間以内で紐つけられた投稿が多い順）
    public function testGetTrend()
    {
        $user = factory(User::class)->create();

        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();
        $tag3 = factory(Tag::class)->create();
        $tag4 = factory(Tag::class)->create();

        $post1 = factory(Post::class)->create([
            'created_at' => new Carbon('-1 day', 'Asia/Tokyo'),
        ]);
        $post2 = factory(Post::class)->create([
            'created_at' => new Carbon('-2 day', 'Asia/Tokyo'),
        ]);
        $post3 = factory(Post::class)->create([
            'created_at' => new Carbon('-3 day', 'Asia/Tokyo'),
        ]);
        $post4 = factory(Post::class)->create([
            'created_at' => new Carbon('-4 day', 'Asia/Tokyo'),
        ]);
        $post5 = factory(Post::class)->create([
            'created_at' => new Carbon('-5 day', 'Asia/Tokyo'),
        ]);
        $post6 = factory(Post::class)->create([
            'created_at' => new Carbon('-6 day', 'Asia/Tokyo'),
        ]);
        $post7 = factory(Post::class)->create([
            'created_at' => new Carbon('-300 day', 'Asia/Tokyo'),
        ]);

        $tag1->posts()->attach([$post1->id, $post2->id]);
        $tag2->posts()->attach([$post3->id, $post4->id, $post5->id]);
        $tag3->posts()->attach([$post6->id]);
        $tag4->posts()->attach([$post7->id]);

        $this->getJson('/api/tag/trend')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/tag/trend')
            ->assertOk()
            ->assertSeeInOrder([
                $tag2->name, '"posts_count":3',
                $tag1->name, '"posts_count":2',
                $tag3->name, '"posts_count":1',
                $tag4->name, '"posts_count":0',
            ]);
    }


    // そのタグを関連づけた投稿の数を取得
    public function testTagCount()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create();

        factory(PostTag::class, 3)->create(['tag_id' => $tag->id]);

        $this->getJson('/api/tag/count/' . $tag->name)
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/tag/count/' . $tag->name)
            ->assertOk()
            ->assertSee(3);
    }


    // そのタグを関連づけた投稿を新着順で取得
    public function testGetTagsNewPosts()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create();

        $post1 = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();
        $post3 = factory(Post::class)->create();
        $post4 = factory(Post::class)->create();
        $post5 = factory(Post::class)->create();

        $tag->posts()->attach([
            $post1->id,
            $post2->id,
            $post3->id,
            $post4->id,
            $post5->id,
        ]);

        $firstPostIdPlusOne = $post5->id + 1;
        
        $this->getJson('/api/post/tags/new/' . $tag->name . '?last_post_id=' . $firstPostIdPlusOne)
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/post/tags/new/' . $tag->name . '?last_post_id=' . $firstPostIdPlusOne)
            ->assertOk()
            ->assertSeeInOrder([
                $post5->body,
                $post4->body,
                $post3->body,
            ])
            ->assertJsonFragment(['lastPostId' => $post1->id]);

        $this->getJson('/api/post/tags/new/' . $tag->name . '?last_post_id=' . $post3->id)
            ->assertOk()
            ->assertSeeInOrder([
                $post2->body,
                $post1->body,
            ])
            ->assertJsonFragment(['lastPostId' => $post1->id]);
    }


    // タグページで投稿のみをどんまい数が多い順で取得
    public function testGetTagsPopularPosts()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create();

        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create();

        $post1 = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();
        $post3 = factory(Post::class)->create();
        $post4 = factory(Post::class)->create();
        $post5 = factory(Post::class)->create();

        factory(Donmai::class, 5)->create(['post_id' => $post5->id]);
        factory(Donmai::class, 4)->create(['post_id' => $post4->id]);
        factory(Donmai::class, 3)->create(['post_id' => $post3->id]);
        factory(Donmai::class, 2)->create(['post_id' => $post2->id]);
        factory(Donmai::class, 1)->create(['post_id' => $post1->id]);

        $tag->posts()->attach([
            $post1->id,
            $post2->id,
            $post3->id,
            $post4->id,
            $post5->id,
        ]);

        $this->getJson('/api/post/tags/popular/' . $tag->name . '?loaded_post_ids=')
            ->assertStatus(401);

        $this->actingAs($user)
            ->getJson('/api/post/tags/popular/' . $tag->name . '?loaded_post_ids=')
            ->assertOk()
            ->assertSeeInOrder([
                $post5->body,
                $post4->body,
                $post3->body,
            ])
            ->assertJsonFragment(['postsTotal' => 5]);

        $this->getJson('/api/post/tags/popular/' . $tag->name . '?loaded_post_ids=' . $post5->id . '-' . $post4->id . '-' . $post3->id)
            ->assertOk()
            ->assertSeeInOrder([
                $post2->body,
                $post1->body,
            ])
            ->assertJsonFragment(['postsTotal' => 5]);
    }
}
