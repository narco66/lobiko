<?php

namespace Tests\Feature\Referentiels;

use App\Models\StructureMedicale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructureMedicaleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_structure_crud_via_modele(): void
    {
        // Create
        $structure = StructureMedicale::factory()->create([
            'nom' => 'Clinique Test',
            'ville' => 'Libreville',
        ]);
        $this->assertNotNull($structure->id);

        // Read
        $found = StructureMedicale::find($structure->id);
        $this->assertEquals('Clinique Test', $found->nom);

        // Update
        $structure->update(['ville' => 'Franceville']);
        $this->assertEquals('Franceville', $structure->fresh()->ville);

        // Delete
        $structure->delete();
        $this->assertNull(StructureMedicale::find($structure->id));
    }
}
