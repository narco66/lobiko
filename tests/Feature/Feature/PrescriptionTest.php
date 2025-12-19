<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\TestsBaseSeeder;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $this->seed(TestsBaseSeeder::class);
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
