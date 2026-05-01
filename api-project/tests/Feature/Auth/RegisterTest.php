<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\Country;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'influencer',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user_id', 'token']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'influencer',
        ]);
    }

    public function test_user_can_register_with_country(): void
    {
        $country = Country::factory()->create(['code' => 'XX', 'name' => 'Testland']);

        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'brand@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'brand',
            'country_id' => $country->id,
        ]);

        $response->assertCreated();

        $user = User::where('email', 'brand@example.com')->first();
        $this->assertSame(Role::Brand, $user->role);
        $this->assertSame($country->id, $user->country_id);
    }

    public function test_registered_user_receives_verification_email(): void
    {
        $this->skipUnlessUserMustVerifyEmail();

        Notification::fake();

        $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'influencer',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_fails_without_required_fields(): void
    {
        $response = $this->postJson(route('register'), []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function test_registration_fails_with_invalid_email(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'influencer',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => 'influencer',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_with_mismatched_password_confirmation(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
            'role' => 'influencer',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'influencer',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_invalid_role(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'super-admin',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);
    }

    public function test_registration_fails_with_nonexistent_country(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'influencer',
            'country_id' => 999999,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['country_id']);
    }
}
