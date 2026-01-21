<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Event::fake(); // Tangkap event Registered

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'no_hp' => '081234567890', // Kolom wajib
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // 1. Pastikan Event Registered terpanggil
        Event::assertDispatched(Registered::class);

        // 2. Cek user masuk database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'no_hp' => '081234567890',
            'is_admin' => 0 // Pastikan default false
        ]);

        // 3. Sesuai Controller Anda: Redirect ke LOGIN (Bukan dashboard)
        $response->assertRedirect(route('login'));
        
        // 4. Pastikan user BELUM login (karena baris Auth::login di-komen)
        $this->assertGuest(); 
    }

    public function test_registration_fails_validation(): void
    {
        // Coba register tanpa no_hp dan password beda
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'no_hp' => '', // Kosong
            'password' => 'password',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['no_hp', 'password']);
    }
}