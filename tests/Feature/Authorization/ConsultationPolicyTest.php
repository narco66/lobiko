<?php

namespace Tests\Feature\Authorization;

use App\Models\Consultation;
use App\Models\User;
use App\Policies\ConsultationPolicy;
use Tests\TestCase;

class ConsultationPolicyTest extends TestCase
{
    public function test_patient_can_view_own_consultation_and_cannot_update(): void
    {
        $patient = $this->fakeUser('patient-1', ['patient']);
        $pro = $this->fakeUser('pro-1', ['medecin']);

        $consultation = Consultation::make([
            'patient_id' => $patient->id,
            'professionnel_id' => $pro->id,
        ]);

        $this->assertTrue((new ConsultationPolicy())->view($patient, $consultation));
        $this->assertFalse((new ConsultationPolicy())->update($patient, $consultation));
    }

    public function test_professional_assignee_can_update(): void
    {
        $pro = $this->fakeUser('pro-1', ['medecin']);
        $consultation = Consultation::make([
            'patient_id' => 'patient-x',
            'professionnel_id' => $pro->id,
        ]);

        $policy = new ConsultationPolicy();
        $this->assertTrue($policy->update($pro, $consultation));
    }

    public function test_professional_not_assigned_cannot_view_or_update(): void
    {
        $pro = $this->fakeUser('pro-1', ['medecin']);
        $consultation = Consultation::make([
            'patient_id' => 'patient-x',
            'professionnel_id' => 'other-pro',
        ]);

        $policy = new ConsultationPolicy();
        $this->assertFalse($policy->view($pro, $consultation));
        $this->assertFalse($policy->update($pro, $consultation));
    }

    public function test_patient_cannot_view_or_update_other_consultation(): void
    {
        $patient = $this->fakeUser('patient-1', ['patient']);
        $consultation = Consultation::make([
            'patient_id' => 'patient-2',
            'professionnel_id' => 'pro-x',
        ]);

        $policy = new ConsultationPolicy();
        $this->assertFalse($policy->view($patient, $consultation));
        $this->assertFalse($policy->update($patient, $consultation));
    }

    public function test_unprivileged_user_is_denied(): void
    {
        $user = $this->fakeUser('u1', []); // sans rôle
        $consultation = Consultation::make();

        $policy = new ConsultationPolicy();
        $this->assertFalse($policy->view($user, $consultation));
        $this->assertFalse($policy->update($user, $consultation));
    }

    public function test_admin_is_authorized(): void
    {
        $admin = $this->fakeUser('admin-1', ['admin']);
        $consultation = Consultation::make();

        $this->assertTrue((new ConsultationPolicy())->view($admin, $consultation));
        $this->assertTrue((new ConsultationPolicy())->update($admin, $consultation));
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
