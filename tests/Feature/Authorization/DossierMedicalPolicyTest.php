<?php

namespace Tests\Feature\Authorization;

use App\Models\DossierMedical;
use App\Models\User;
use App\Policies\DossierMedicalPolicy;
use Tests\TestCase;

class DossierMedicalPolicyTest extends TestCase
{
    public function test_patient_can_view_own_dossier_and_not_update(): void
    {
        $patient = $this->fakeUser('patient-1', ['patient']);
        $dossier = DossierMedical::make(['patient_id' => $patient->id]);

        $policy = new DossierMedicalPolicy();
        $this->assertTrue($policy->view($patient, $dossier));
        $this->assertFalse($policy->update($patient, $dossier));
    }

    public function test_medecin_can_update_dossier(): void
    {
        $medecin = $this->fakeUser('med-1', ['medecin']);
        $dossier = DossierMedical::make();

        $policy = new DossierMedicalPolicy();
        $this->assertFalse($policy->view($medecin, $dossier));
        $this->assertFalse($policy->update($medecin, $dossier));
    }

    public function test_patient_cannot_view_other_patient_dossier(): void
    {
        $patient = $this->fakeUser('patient-1', ['patient']);
        $dossier = DossierMedical::make(['patient_id' => 'patient-2']);

        $policy = new DossierMedicalPolicy();
        $this->assertFalse($policy->view($patient, $dossier));
        $this->assertFalse($policy->update($patient, $dossier));
    }

    public function test_unprivileged_user_is_denied(): void
    {
        $user = $this->fakeUser('u1', []); // sans rôle
        $dossier = DossierMedical::make();

        $policy = new DossierMedicalPolicy();
        $this->assertFalse($policy->view($user, $dossier));
        $this->assertFalse($policy->update($user, $dossier));
    }

    public function test_admin_is_authorized(): void
    {
        $admin = $this->fakeUser('admin-1', ['admin']);
        $dossier = DossierMedical::make();

        $this->assertTrue((new DossierMedicalPolicy())->view($admin, $dossier));
        $this->assertTrue((new DossierMedicalPolicy())->update($admin, $dossier));
    }

    public function test_super_admin_is_authorized(): void
    {
        $admin = $this->fakeUser('super-1', ['super-admin']);
        $dossier = DossierMedical::make();

        $this->assertTrue((new DossierMedicalPolicy())->view($admin, $dossier));
        $this->assertTrue((new DossierMedicalPolicy())->update($admin, $dossier));
    }

    private function fakeUser(string $id, array $roles): User
    {
        $user = new class extends User {
            public array $fakeRoles = [];
            public function hasAnyRole(...$roles): bool
            {
                $flat = [];
                array_walk_recursive($roles, function ($r) use (&$flat) {
                    $flat[] = $r;
                });
                return !empty(array_intersect($flat, $this->fakeRoles));
            }
        };
        $user->setAttribute('id', $id);
        $user->fakeRoles = $roles;
        return $user;
    }
}
