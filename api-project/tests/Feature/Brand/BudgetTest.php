<?php

namespace Tests\Feature\Brand;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_can_set_and_view_budget(): void
    {
        $brand = User::factory()->brand()->create();

        $update = $this->actingAs($brand)->patchJson(route('budget.update'), [
            'monthly_budget_cents' => 1000000,
        ]);

        $update->assertOk()->assertJsonPath('data.monthly_budget_cents', 1000000);
    }

    public function test_budget_aggregates_monthly_spend(): void
    {
        $brand = User::factory()->brand()->create(['monthly_budget_cents' => 1000000]);

        Payment::factory()->escrowed()->create(['brand_id' => $brand->id, 'amount_cents' => 200000]);
        Payment::factory()->released()->create(['brand_id' => $brand->id, 'amount_cents' => 100000]);
        Payment::factory()->released()->create(); // unrelated brand

        $response = $this->actingAs($brand)->getJson(route('budget.show'));

        $response->assertOk()
            ->assertJsonPath('data.month_spend_cents', 300000)
            ->assertJsonPath('data.month_remaining_cents', 700000)
            ->assertJsonPath('data.month_pct_used', 30);
    }

    public function test_influencer_cannot_view_brand_budget(): void
    {
        $influencer = User::factory()->create();

        $response = $this->actingAs($influencer)->getJson(route('budget.show'));

        $response->assertForbidden();
    }
}
