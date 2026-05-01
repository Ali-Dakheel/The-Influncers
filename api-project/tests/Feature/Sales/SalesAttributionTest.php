<?php

namespace Tests\Feature\Sales;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAttributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_sales_rep_to_brand(): void
    {
        $admin = User::factory()->admin()->create();
        $rep = User::factory()->admin()->create();
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($admin)->patchJson(route('sales.assign', $brand), [
            'sales_rep_id' => $rep->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.sales_rep_id', $rep->id);

        $this->assertSame($rep->id, $brand->fresh()->sales_rep_id);
    }

    public function test_admin_sees_attribution_summary(): void
    {
        $admin = User::factory()->admin()->create();
        $rep = User::factory()->admin()->create();

        $brandA = User::factory()->brand()->create(['sales_rep_id' => $rep->id]);
        $brandB = User::factory()->brand()->create(['sales_rep_id' => $rep->id]);
        $unrepresented = User::factory()->brand()->create();

        Payment::factory()->released()->create(['brand_id' => $brandA->id, 'amount_cents' => 100000]);
        Payment::factory()->escrowed()->create(['brand_id' => $brandA->id, 'amount_cents' => 50000]);
        Payment::factory()->released()->create(['brand_id' => $brandB->id, 'amount_cents' => 200000]);
        Payment::factory()->released()->create(['brand_id' => $unrepresented->id, 'amount_cents' => 999999]);

        // Login as the rep, not the admin who created them, to make the test focused
        // (sales_rep view needs admin privileges; rep is admin in this fixture)
        $response = $this->actingAs($rep)->getJson(route('sales.attribution'));

        $response->assertOk()
            ->assertJsonPath('data.brand_count', 2)
            ->assertJsonPath('data.total_revenue_cents', 350000);
    }

    public function test_non_admin_cannot_assign_sales_rep(): void
    {
        $brandUser = User::factory()->brand()->create();
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brandUser)->patchJson(route('sales.assign', $brand), [
            'sales_rep_id' => null,
        ]);

        $response->assertForbidden();
    }
}
