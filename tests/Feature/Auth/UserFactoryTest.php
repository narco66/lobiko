<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_persists_with_required_fields(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotEmpty($user->adresse_ville);
    }
}

