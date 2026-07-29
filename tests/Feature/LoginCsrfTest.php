<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginCsrfTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the 419 error view contains the back link.
     */
    public function test_419_view_contains_previous_url()
    {
        // Set the previous URL in the session/request context
        $this->withSession(['_previous' => ['url' => 'http://localhost/login']]);
        
        $view = $this->view('errors.419');

        $view->assertSee('http://localhost/login');
        $view->assertSee('تحديث الصفحة');
    }
}
