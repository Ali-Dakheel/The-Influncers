<?php

namespace Tests\Feature\Locale;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_query_overrides_default(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('portfolio.mine').'?locale=sv');

        $response->assertOk();
        $this->assertSame('sv', $response->headers->get('Content-Language'));
    }

    public function test_accept_language_header_resolves(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['Accept-Language' => 'sv-SE,sv;q=0.9,en;q=0.8'])
            ->getJson(route('portfolio.mine'));

        $response->assertOk();
        $this->assertSame('sv', $response->headers->get('Content-Language'));
    }

    public function test_unsupported_locale_falls_back_to_english(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['Accept-Language' => 'fr-FR'])
            ->getJson(route('portfolio.mine'));

        $response->assertOk();
        $this->assertSame('en', $response->headers->get('Content-Language'));
    }

    public function test_translation_strings_load(): void
    {
        $this->assertSame("You're in! Application accepted.", __('messages.application.accepted', [], 'en'));
        $this->assertSame('Du är med! Ansökan accepterad.', __('messages.application.accepted', [], 'sv'));
    }
}
