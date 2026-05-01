<?php

namespace Tests\Feature\Sales;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_top_accounts_and_churn_risk(): void
    {
        $rep = User::factory()->admin()->create();

        $bigSpender = User::factory()->brand()->create(['sales_rep_id' => $rep->id, 'name' => 'Big Co']);
        $mediumSpender = User::factory()->brand()->create(['sales_rep_id' => $rep->id, 'name' => 'Med Co']);
        $churning = User::factory()->brand()->create(['sales_rep_id' => $rep->id, 'name' => 'Quiet Co']);
        $unrelated = User::factory()->brand()->create(); // not in this rep's portfolio

        Payment::factory()->released()->count(3)->create(['brand_id' => $bigSpender->id, 'amount_cents' => 500000]);
        Payment::factory()->released()->create(['brand_id' => $mediumSpender->id, 'amount_cents' => 200000]);
        Payment::factory()->released()->create([
            'brand_id' => $churning->id,
            'amount_cents' => 100000,
            'created_at' => now()->subDays(90), // outside 60-day window → churn risk
        ]);
        Payment::factory()->released()->create(['brand_id' => $unrelated->id, 'amount_cents' => 999999]);

        $response = $this->actingAs($rep)->getJson(route('sales.dashboard'));

        $response->assertOk()
            ->assertJsonPath('data.brand_count', 3)
            ->assertJsonPath('data.top_accounts.0.brand_name', 'Big Co')
            ->assertJsonPath('data.top_accounts.0.total_spend_cents', 1500000);

        $churnNames = collect($response->json('data.churn_risk'))->pluck('brand_name');
        $this->assertTrue($churnNames->contains('Quiet Co'));
        $this->assertFalse($churnNames->contains('Big Co'));
    }

    public function test_non_admin_blocked(): void
    {
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->getJson(route('sales.dashboard'));

        $response->assertForbidden();
    }
}
