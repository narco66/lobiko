<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    /**
     * Les pages publiques principales doivent répondre.
     */
    public function test_contact_page_is_accessible(): void
    {
        $this->get('/contact')->assertOk();
    }

    public function test_services_public_pages_are_accessible(): void
    {
        $this->get('/services/teleconsultation')
            ->assertOk()
            ->assertSee('Téléconsultation sécurisée');

        $this->get('/services/appointment')
            ->assertOk()
            ->assertSee('Prise de rendez-vous');

        $this->get('/services/pharmacy')
            ->assertOk()
            ->assertSee('Pharmacie en ligne');

        $this->get('/services/insurance')
            ->assertOk()
            ->assertSee('Assurance santé intégrée');

        $this->get('/services/emergency')
            ->assertOk()
            ->assertSee('Parcours urgences');

        $this->get('/professionals')
            ->assertOk()
            ->assertSee('Trouver un professionnel de santé');
    }
}
