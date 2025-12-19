<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DoctorsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('super-admin', 'web');
    }

    public function test_guest_cannot_access_doctors(): void
    {
        $this->get(route('admin.doctors.index'))->assertRedirect('/login');
    }

    public function test_super_admin_can_list_doctors(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        Doctor::factory()->create(['matricule' => 'DOC-TEST', 'nom' => 'Test', 'prenom' => 'Doc']);

        $this->actingAs($user)
            ->get(route('admin.doctors.index'))
            ->assertOk()
            ->assertSee('DOC-TEST');
    }
}
