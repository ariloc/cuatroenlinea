<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicFunctionality extends TestCase
{
    /**
     * Basic website functionality.
     *
     * @return void
     */
    public function test_url()
    {
        $response = $this->get('/jugar/1');

        $response->assertStatus(200);
    }
}
