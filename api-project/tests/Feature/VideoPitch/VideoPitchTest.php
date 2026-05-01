<?php

namespace Tests\Feature\VideoPitch;

use App\Enums\VideoPitchStatus;
use App\Models\User;
use App\Models\VideoPitch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoPitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_send_pitch_to_brand(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();

        $response = $this->actingAs($influencer)->postJson(route('pitches.send'), [
            'brand_id' => $brand->id,
            'video_url' => 'https://example.com/pitch.mp4',
            'message' => 'I would love to work with your brand on a sneaker drop.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.brand_id', $brand->id);
    }

    public function test_brand_cannot_send_pitch(): void
    {
        $brand = User::factory()->brand()->create();
        $other = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->postJson(route('pitches.send'), [
            'brand_id' => $other->id,
            'video_url' => 'https://example.com/x.mp4',
        ]);

        $response->assertForbidden();
    }

    public function test_pitch_target_must_be_a_brand(): void
    {
        $influencerA = User::factory()->create();
        $influencerB = User::factory()->create();

        $response = $this->actingAs($influencerA)->postJson(route('pitches.send'), [
            'brand_id' => $influencerB->id,
            'video_url' => 'https://example.com/x.mp4',
        ]);

        $response->assertStatus(422);
    }

    public function test_brand_can_accept_pitch(): void
    {
        $brand = User::factory()->brand()->create();
        $pitch = VideoPitch::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('pitches.accept', $pitch));

        $response->assertOk()->assertJsonPath('data.status', 'accepted');
        $this->assertSame(VideoPitchStatus::Accepted, $pitch->fresh()->status);
    }

    public function test_brand_can_reject_pitch(): void
    {
        $brand = User::factory()->brand()->create();
        $pitch = VideoPitch::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('pitches.reject', $pitch), ['note' => 'Not a fit']);

        $response->assertOk()->assertJsonPath('data.status', 'rejected');
    }

    public function test_other_brand_cannot_decide_pitch(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        $pitch = VideoPitch::factory()->create(['brand_id' => $brandB->id]);

        $response = $this->actingAs($brandA)->postJson(route('pitches.accept', $pitch));

        $response->assertForbidden();
    }

    public function test_cannot_decide_already_decided_pitch(): void
    {
        $brand = User::factory()->brand()->create();
        $pitch = VideoPitch::factory()->accepted()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('pitches.accept', $pitch));

        $response->assertUnprocessable()->assertJsonValidationErrors(['status']);
    }
}
