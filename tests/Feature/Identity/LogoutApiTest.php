<?php

declare(strict_types=1);

namespace Tests\Feature\Identity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\FeatureTestCase;

class LogoutApiTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[Test]
    public function 正常系_ログアウト成功(): void
    {
        // Arrange
        /** @var User $user */
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // トークンが存在することを確認
        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser);
        $this->assertCount(1, $freshUser->tokens);

        // Act
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson(route('api.auth.logout'));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);

        // トークンが削除されていることを確認
        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser);
        $this->assertCount(0, $freshUser->tokens);
    }

    #[Test]
    public function 異常系_未認証ユーザーのログアウト試行(): void
    {
        // Arrange - 未認証状態

        // Act
        $response = $this->postJson(route('api.auth.logout'));

        // Assert
        $response->assertUnauthorized();
    }

    #[Test]
    public function 異常系_無効なトークンでのログアウト試行(): void
    {
        // Arrange
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ])->postJson(route('api.auth.logout'));

        // Assert
        $response->assertUnauthorized();
    }
}
