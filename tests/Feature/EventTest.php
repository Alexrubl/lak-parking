<?php

namespace Tests\Feature;

use App\Models\Controller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_Проверка_события_Event(): void
    {
        $controller = Controller::factory()->create();
        $response = $this->post('/api/event', [
            'apikey' => '87654321',
            'ev_date' => '2024-09-14 22:27:00',
            'plate' => 'A223CD28',
            'access' => '1',
            'entry' => 'in',
        ]);

        $response->assertStatus(200);
    }
}
