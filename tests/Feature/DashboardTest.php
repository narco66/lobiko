<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_sees_admin_kpis(): void
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Utilisateurs')
            ->assertDontSee('notes_privees');
    }

    public function test_patient_sees_patient_kpis(): void
    {
        Role::findOrCreate('patient', 'web');
        $user = User::factory()->create();
        $user->assignRole('patient');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Consultations')
            ->assertDontSee('notes_privees');
    }
}
