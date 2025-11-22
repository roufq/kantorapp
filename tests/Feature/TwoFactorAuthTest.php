<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_access_2fa_setup_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/2fa/setup');

        $response->assertStatus(200);
        $response->assertViewIs('auth.2fa.setup');
    }

    /** @test */
    public function user_can_enable_email_2fa()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/2fa/setup', [
            'method' => 'email',
        ]);

        $response->assertRedirect('/2fa/verify');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'two_factor_method' => 'email',
            'two_factor_enabled' => false, // Not confirmed yet
        ]);
    }

    /** @test */
    public function user_can_enable_sms_2fa()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/2fa/setup', [
            'method' => 'sms',
            'phone_number' => '+1234567890',
        ]);

        $response->assertRedirect('/2fa/verify');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'two_factor_method' => 'sms',
            'phone_number' => '+1234567890',
            'two_factor_enabled' => false,
        ]);
    }

    /** @test */
    public function user_can_enable_authenticator_app_2fa()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/2fa/setup', [
            'method' => 'app',
        ]);

        $response->assertRedirect('/2fa/verify-app');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'two_factor_method' => 'app',
            'two_factor_enabled' => false,
        ]);
    }

    /** @test */
    public function user_can_disable_2fa()
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_method' => 'email',
        ]);

        $response = $this->actingAs($user)->delete('/2fa/disable');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'two_factor_enabled' => false,
            'two_factor_method' => null,
        ]);
    }

    /** @test */
    public function has_two_factor_enabled_method_works()
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
        ]);

        $this->assertTrue($user->hasTwoFactorEnabled());

        $user->update(['two_factor_enabled' => false]);
        $this->assertFalse($user->hasTwoFactorEnabled());
    }

    /** @test */
    public function generate_backup_codes_creates_10_codes()
    {
        $user = User::factory()->create();

        $codes = $user->generateBackupCodes();

        $this->assertCount(10, $codes);
        $this->assertIsArray($codes);

        // Check that codes are stored in database
        $user->refresh();
        $this->assertNotNull($user->two_factor_backup_codes);
    }

    /** @test */
    public function verify_backup_code_works()
    {
        $user = User::factory()->create();
        $codes = $user->generateBackupCodes();

        $firstCode = $codes[0];

        $this->assertTrue($user->verifyBackupCode($firstCode));

        // Code should be removed after use
        $this->assertFalse($user->verifyBackupCode($firstCode));
    }
}
