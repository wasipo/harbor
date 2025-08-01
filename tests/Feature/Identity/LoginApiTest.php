<?php

declare(strict_types=1);

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\FeatureTestCase;

class LoginApiTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[Test]
    public function 正常系_ログイン成功(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'is_active',
                        'email_verified_at',
                        'categoryIds',
                        'roleIds',
                    ],
                    'token',
                    'expires_at',
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'api-token',
        ]);
    }

    #[Test]
    public function 異常系_無効な認証情報(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function 異常系_非アクティブユーザー(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function 異常系_バリデーションエラー(): void
    {
        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
