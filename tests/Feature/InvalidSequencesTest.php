<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvalidSequences extends TestCase
{
    /**
     * Test error handling when entering an incorrectly formatted url.
     *
     * @return void
     */
    public function test_char_in_sequence() {
		$response = $this->get('/jugar/a');
		$response->assertStatus(200);
    }

	public function test_out_of_bounds_right() {
		$response = $this->get('/jugar/8');
		$response->assertStatus(200);
	}
	
	public function test_out_of_bounds_left() {
		$response = $this->get('/jugar/0');
		$response->assertStatus(200);
	}

	public function test_out_of_bounds_top() {
		for ($i = 1; $i <= 7; $i++) {
			$response = $this->get('/jugar/' . str_repeat($i+'0', 7));
			$response->assertStatus(200);
		}
	}	
	
	public function test_long_sequence() { // has 43 > 42 elements in sequence
		$response = $this->get('jugar/1234567123456712345671234567123456712345671');
		$response->assertStatus(200);
	}
}
