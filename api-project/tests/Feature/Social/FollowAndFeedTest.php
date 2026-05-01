<?php

namespace Tests\Feature\Social;

use App\Models\Campaign;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowAndFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_and_unfollow(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $follow = $this->actingAs($a)->postJson(route('users.follow', $b));
        $follow->assertCreated();
        $this->assertDatabaseHas('follows', ['follower_id' => $a->id, 'followed_id' => $b->id]);

        $unfollow = $this->actingAs($a)->deleteJson(route('users.unfollow', $b));
        $unfollow->assertNoContent();
        $this->assertDatabaseMissing('follows', ['follower_id' => $a->id, 'followed_id' => $b->id]);
    }

    public function test_cannot_follow_self(): void
    {
        $a = User::factory()->create();

        $response = $this->actingAs($a)->postJson(route('users.follow', $a));

        $response->assertStatus(422);
    }

    public function test_following_is_idempotent(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->actingAs($a)->postJson(route('users.follow', $b));
        $this->actingAs($a)->postJson(route('users.follow', $b));

        $this->assertSame(1, Follow::where('follower_id', $a->id)->where('followed_id', $b->id)->count());
    }

    public function test_feed_shows_published_campaigns_from_followed_brands(): void
    {
        $follower = User::factory()->create();
        $brand = User::factory()->brand()->create();
        $unrelated = User::factory()->brand()->create();

        Follow::create(['follower_id' => $follower->id, 'followed_id' => $brand->id]);

        Campaign::factory()->published()->create(['brand_id' => $brand->id, 'title' => 'Visible']);
        Campaign::factory()->published()->create(['brand_id' => $unrelated->id, 'title' => 'Hidden']);

        $response = $this->actingAs($follower)->getJson(route('feed'));

        $response->assertOk();
        $titles = collect($response->json('data'))->pluck('campaign_title');
        $this->assertTrue($titles->contains('Visible'));
        $this->assertFalse($titles->contains('Hidden'));
    }

    public function test_empty_feed_when_following_nobody(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('feed'));

        $response->assertOk()->assertJsonCount(0, 'data');
    }
}
