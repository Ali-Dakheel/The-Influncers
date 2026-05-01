<?php

namespace Tests\Feature\Creator;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_sees_empty_portfolio_initially(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('portfolio.mine'));

        $response->assertOk()
            ->assertJsonPath('data.bio', null)
            ->assertJsonPath('data.user_id', $user->id);
    }

    public function test_influencer_can_update_portfolio(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patchJson(route('portfolio.update'), [
            'bio' => 'I make videos about sneakers.',
            'content_style' => ['lifestyle', 'fashion'],
            'audience_size' => 50000,
            'past_collabs' => [
                ['brand' => 'Nike', 'year' => 2025, 'deliverables' => '3 reels', 'link' => 'https://example.com/x'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.bio', 'I make videos about sneakers.')
            ->assertJsonPath('data.audience_size', 50000)
            ->assertJsonCount(2, 'data.content_style');
    }

    public function test_brand_can_view_influencer_portfolio(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();

        $response = $this->actingAs($brand)->getJson(route('portfolio.show', $influencer));

        $response->assertOk();
    }

    public function test_brand_cannot_get_brand_portfolio(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();

        $response = $this->actingAs($brandA)->getJson(route('portfolio.show', $brandB));

        $response->assertNotFound();
    }
}
