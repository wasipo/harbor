<?php

declare(strict_types=1);

namespace Tests\Integration\Application\Identity;

use App\Adapter\Identity\AuthSessionManager;
use App\Application\Identity\LogoutAction;
use App\Domain\Shared\Contracts\LoggerInterface;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogoutActionTest extends TestCase
{
    use RefreshDatabase;
    
    private LogoutAction $action;
    private AuthSessionManager $authSessionManager;
    /** @var LoggerInterface&MockObject */
    private LoggerInterface $logger;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->authSessionManager = new AuthSessionManager();
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->action = new LogoutAction(
            authSessionManager: $this->authSessionManager,
            logger: $this->logger
        );
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function 正常系_ログアウト成功(): void
    {
        // Arrange
        $user = User::factory()->create([
            'ulid' => '01HZKT234567890ABCDEFGHIJK'
        ]);
        Auth::login($user);
        
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(function ($message, $context) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount === 1) {
                    $this->assertEquals('Logout attempt started', $message);
                    $this->assertEquals(['user_id' => '01HZKT234567890ABCDEFGHIJK'], $context);
                } elseif ($callCount === 2) {
                    $this->assertEquals('Logout successful', $message);
                    $this->assertEquals(['user_id' => '01HZKT234567890ABCDEFGHIJK'], $context);
                }
            });

        // Act
        ($this->action)();

        // Assert
        $this->assertGuest();
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function 異常系_未認証状態でのログアウト試行(): void
    {
        // Arrange - 未認証状態
        Auth::logout();
        
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                'Logout failed',
                $this->callback(function ($context) {
                    return isset($context['error']) && isset($context['trace']);
                })
            );

        // Act & Assert
        $this->expectException(RuntimeException::class);
        
        ($this->action)();
    }
}