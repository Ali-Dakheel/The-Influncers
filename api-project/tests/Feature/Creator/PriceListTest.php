<?php

namespace Tests\Feature\Creator;

use App\Models\PriceListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_set_price_list_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('price-list.items.set'), [
            'platform' => 'instagram',
            'format' => 'reel',
            'price_cents' => 250000,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.platform', 'instagram')
            ->assertJsonPath('data.price_cents', 250000);

        $this->assertDatabaseHas('price_list_items', [
            'user_id' => $user->id,
            'platform' => 'instagram',
            'format' => 'reel',
        ]);
    }

    public function test_setting_same_platform_format_updates_price(): void
    {
        $user = User::factory()->create();
        PriceListItem::factory()->create([
            'user_id' => $user->id,
            'platform' => 'instagram',
            'format' => 'reel',
            'price_cents' => 100000,
        ]);

        $response = $this->actingAs($user)->postJson(route('price-list.items.set'), [
            'platform' => 'instagram',
            'format' => 'reel',
            'price_cents' => 300000,
        ]);

        $response->assertCreated()->assertJsonPath('data.price_cents', 300000);
        $this->assertSame(1, PriceListItem::where('user_id', $user->id)->count());
    }

    public function test_brand_cannot_set_price_list(): void
    {
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->postJson(route('price-list.items.set'), [
            'platform' => 'instagram',
            'format' => 'reel',
            'price_cents' => 100000,
        ]);

        $response->assertForbidden();
    }

    public function test_create_price_package(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('price-list.packages.create'), [
            'name' => '5+5+5 Bundle',
            'description' => 'Cross-platform launch',
            'items' => [
                ['platform' => 'instagram', 'format' => 'reel', 'quantity' => 5],
                ['platform' => 'tiktok', 'format' => 'video', 'quantity' => 5],
                ['platform' => 'youtube', 'format' => 'video', 'quantity' => 5],
            ],
            'discount_pct' => 20,
            'total_cents' => 800000,
        ]);

        $response->assertCreated()->assertJsonPath('data.discount_pct', 20);
    }

    public function test_view_price_list(): void
    {
        $user = User::factory()->create();
        PriceListItem::factory()->count(3)->sequence(
            ['platform' => 'instagram', 'format' => 'reel'],
            ['platform' => 'tiktok', 'format' => 'video'],
            ['platform' => 'youtube', 'format' => 'video'],
        )->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('price-list.mine'));

        $response->assertOk()->assertJsonCount(3, 'items');
    }
}
