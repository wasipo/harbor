<?php

declare(strict_types=1);

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginWebTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 正常系_ログインフォーム表示(): void
    {
        // Act
        $response = $this->get(route('login'));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Login')
        );
    }

    #[Test]
    public function 正常系_ログイン成功(): void
    {
        // Arrange
        $password = 'password123';
        $user = User::factory()->create([
            'password' => bcrypt($password),
            'is_active' => true,
        ]);

        // Act
        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => $password,
            'remember' => false,
        ]);

        // Assert
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $response->assertSessionHas('success');
    }

    #[Test]
    public function 異常系_無効な認証情報(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'wrong-password',
            'remember' => false,
        ]);

        // Assert
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function 異常系_非アクティブユーザー(): void
    {
        // Arrange
        $password = 'password123';
        $user = User::factory()->create([
            'password' => bcrypt($password),
            'is_active' => false,
        ]);

        // Act
        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => $password,
            'remember' => false,
        ]);

        // Assert
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}