<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        // La app requiere estar "activada" (ver CheckAppActivation) para acceder a cualquier ruta.
        DB::table('app_settings')->insert([
            'key' => 'app_activated',
            'value' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
