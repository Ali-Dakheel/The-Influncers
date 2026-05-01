<?php

namespace Tests\Feature\Payment;

use App\Enums\InvoiceKind;
use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Draft;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_application_escrows_payment(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
            'proposed_price_cents' => 250000,
        ]);

        $this->actingAs($brand)->postJson(route('applications.accept', $application))->assertOk();

        $payment = Payment::where('application_id', $application->id)->first();
        $this->assertNotNull($payment);
        $this->assertSame(PaymentStatus::Escrowed, $payment->status);
        $this->assertSame(250000, $payment->amount_cents);
        $this->assertNotNull($payment->stripe_payment_intent_id);
        $this->assertNotNull($payment->escrowed_at);
    }

    public function test_completing_campaign_releases_payments_and_generates_invoices(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
            'proposed_price_cents' => 200000,
        ]);
        Draft::factory()->approved()->create(['application_id' => $application->id]);

        // Manually escrow (since we created the application as already accepted, the listener didn't run)
        Payment::factory()->escrowed()->create([
            'campaign_id' => $campaign->id,
            'application_id' => $application->id,
            'brand_id' => $brand->id,
            'influencer_id' => $influencer->id,
            'amount_cents' => 200000,
        ]);

        $this->actingAs($brand)->postJson(route('campaigns.complete', $campaign))->assertOk();

        $payment = Payment::where('application_id', $application->id)->first();
        $this->assertSame(PaymentStatus::Released, $payment->status);
        $this->assertNotNull($payment->released_at);
        $this->assertNotNull($payment->stripe_transfer_id);

        $this->assertSame(2, Invoice::where('payment_id', $payment->id)->count());
        $this->assertTrue(Invoice::where('payment_id', $payment->id)->where('kind', InvoiceKind::BrandCharge->value)->exists());
        $this->assertTrue(Invoice::where('payment_id', $payment->id)->where('kind', InvoiceKind::InfluencerPayout->value)->exists());
    }

    public function test_brand_can_list_their_payments(): void
    {
        $brand = User::factory()->brand()->create();
        Payment::factory()->count(3)->create(['brand_id' => $brand->id]);
        Payment::factory()->count(2)->create(); // unrelated

        $response = $this->actingAs($brand)->getJson(route('payments.index'));

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_influencer_sees_only_their_payments(): void
    {
        $influencer = User::factory()->create();
        Payment::factory()->count(2)->create(['influencer_id' => $influencer->id]);
        Payment::factory()->count(4)->create();

        $response = $this->actingAs($influencer)->getJson(route('payments.index'));

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_unrelated_user_cannot_view_payment(): void
    {
        $stranger = User::factory()->create();
        $payment = Payment::factory()->create();

        $response = $this->actingAs($stranger)->getJson(route('payments.show', $payment));

        $response->assertForbidden();
    }
}
