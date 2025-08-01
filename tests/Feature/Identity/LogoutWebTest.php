<?php

declare(strict_types=1);

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogoutWebTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 正常系_ログアウト成功(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->post(route('logout'));

        // Assert
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function 異常系_未認証ユーザーのログアウト試行(): void
    {
        // Arrange - 未認証状態

        // Act
        $response = $this->post(route('logout'));

        // Assert
        $response->assertRedirect('/login');
    }
}