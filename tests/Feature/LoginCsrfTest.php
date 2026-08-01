<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class LoginCsrfTest extends TestCase
{
    use RefreshDatabase;

    public function test_419_view_contains_previous_url()
    {
        $this->withSession(['_previous' => ['url' => 'http://localhost/login']]);
        
        $view = $this->view('errors.419');

        $view->assertSee('http://localhost/login');
        $view->assertSee('تحديث الصفحة');
    }

    public function test_login_succeeds_even_without_csrf_token_due_to_exclusion()
    {
        // Setup user with student role so redirect works normally
        Role::create(['name' => 'student']);
        
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('student');

        // Make a POST request to /login WITHOUT any CSRF token in session or request
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Should authenticate successfully and NOT throw 419
        $this->assertAuthenticatedAs($user);
        $this->assertNotEquals(419, $response->getStatusCode());
        $response->assertStatus(302);
    }

    public function test_login_page_regenerates_csrf_token_and_disables_cache()
    {
        $this->withSession(['_token' => 'stale-token']);

        $response = $this->get('/login');

        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('must-revalidate', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=0', $response->headers->get('Cache-Control'));
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', '0');
        $this->assertNotSame('stale-token', session()->token());
    }

    public function test_token_mismatch_redirects_to_login_with_fresh_token()
    {
        Route::middleware('web')->get('/_test-token-mismatch', function () {
            throw new TokenMismatchException();
        });

        $this->withSession(['_token' => 'stale-token']);

        $response = $this->get('/_test-token-mismatch');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('must-revalidate', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=0', $response->headers->get('Cache-Control'));
        $this->assertNotSame('stale-token', session()->token());
    }
}
