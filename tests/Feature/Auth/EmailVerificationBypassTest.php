<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationBypassTest extends TestCase
{
    use RefreshDatabase;

    public function test_whitelisted_test_account_can_access_without_verification_when_bypass_enabled(): void
    {
        config(['auth.allow_test_account_email_bypass' => true]);

        $user = User::factory()->create([
            'email' => 'admin@lobiko.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_whitelisted_user_without_verification_is_redirected(): void
    {
        config(['auth.allow_test_account_email_bypass' => true]);

        $user = User::factory()->create([
            'email' => 'standard.user@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }
}

