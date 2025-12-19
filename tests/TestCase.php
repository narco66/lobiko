<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Garde-fou : empêcher l'exécution de tests sur un environnement non "testing"
        if (! app()->environment('testing')) {
            throw new \RuntimeException('Tests exécutés hors environnement testing. Vérifiez APP_ENV/DB_DATABASE.');
        }

        // Optionnel : empêcher d'utiliser la base applicative en test
        $db = config('database.connections.' . config('database.default'));
        if (!empty($db['database']) && in_array($db['database'], ['lobiko_db', env('APP_DB_PROD')])) {
            throw new \RuntimeException('La base de test pointe sur la base applicative. Configurez DB_DATABASE=lobiko_test pour les tests.');
        }
    }
}
