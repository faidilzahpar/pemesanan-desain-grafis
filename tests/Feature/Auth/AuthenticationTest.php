<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
            'is_admin' => 0, // Test sebagai user biasa
        ]);

        $response = $this->post('/login', [
            'login' => $user->email, 
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        
        // PERBAIKAN: Controller Anda redirect ke route('home'), bukan 'dashboard'
        $response->assertRedirect(route('home')); 
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'login' => $user->email, // PERBAIKAN: Ganti 'email' jadi 'login'
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
    
    public function test_admin_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'password' => bcrypt('password'),
            'is_admin' => 1, // Test sebagai Admin
        ]);

        $response = $this->post('/login', [
            'login' => $admin->email, // Ganti 'email' jadi 'login'
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        
        // Admin harusnya ke route('admin')
        $response->assertRedirect(route('admin')); 
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}