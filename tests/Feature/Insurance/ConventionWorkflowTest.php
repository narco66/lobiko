<?php

namespace Tests\Feature\Insurance;

use App\Models\Convention;
use App\Models\ConventionRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConventionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_convention_with_rule_can_be_created(): void
    {
        $convention = Convention::factory()->create();
        $rule = ConventionRule::factory()->create(['convention_id' => $convention->id]);

        $this->assertEquals('ACTIVE', $convention->statut);
        $this->assertEquals($convention->id, $rule->convention_id);
        $this->assertEquals(80, $rule->taux_prise_en_charge);
    }
}

