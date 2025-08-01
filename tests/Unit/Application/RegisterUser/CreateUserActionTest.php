<?php

declare(strict_types=1);

namespace Tests\Unit\Application\RegisterUser;

use App\Adapter\RegisterUser\CreateUserCommand;
use App\Application\RegisterUser\CreateUserAction;
use App\Domain\Identity\Email;
use App\Domain\Identity\Name;
use App\Domain\Identity\User;
use App\Domain\Identity\UserRepositoryInterface;
use App\Domain\Shared\Contracts\LoggerInterface;
use DomainException;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\Factories\Domain\Identity\TestUserFactory;
use Tests\UnitTestCase;

class CreateUserActionTest extends UnitTestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private LoggerInterface&MockObject $logger;
    private CreateUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->action = new CreateUserAction(
            $this->userRepository,
            $this->logger
        );
    }

    public function test_正常系_ユーザー作成成功(): void
    {
        // Arrange
        $command = new CreateUserCommand(
            name: 'テストユーザー',
            email: 'test@example.com',
            password: 'password123'
        );

        $expectedUser = TestUserFactory::create(
            name: new Name('テストユーザー'),
            email: new Email('test@example.com')
        );

        $this->userRepository
            ->expects($this->once())
            ->method('existsByEmail')
            ->with($this->equalTo(new Email('test@example.com')))
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->isInstanceOf(User::class),
                'password123'
            )
            ->willReturn($expectedUser);

        $this->logger
            ->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(function () {});

        // Act
        $result = ($this->action)($command);

        // Assert
        $this->assertEquals($expectedUser, $result);
        $this->assertEquals('テストユーザー', $result->name->value);
        $this->assertEquals('test@example.com', $result->email->value);
    }

    public function test_異常系_メールアドレス重複でエラー(): void
    {
        // Arrange
        $command = new CreateUserCommand(
            name: 'テストユーザー',
            email: 'existing@example.com',
            password: 'password123'
        );

        $this->userRepository
            ->expects($this->once())
            ->method('existsByEmail')
            ->with($this->equalTo(new Email('existing@example.com')))
            ->willReturn(true);

        $this->userRepository
            ->expects($this->never())
            ->method('add');

        $this->logger
            ->expects($this->exactly(2))
            ->method($this->logicalOr(
                $this->equalTo('info'),
                $this->equalTo('warning')
            ));

        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('このメールアドレスは既に使用されています');

        ($this->action)($command);
    }

    #[DataProvider('invalidInputProvider')]
    public function test_異常系_無効な入力でエラー(string $name, string $email, string $expectedMessage): void
    {
        // Arrange
        $command = new CreateUserCommand(
            name: $name,
            email: $email,
            password: 'password123'
        );

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with('User creation started', ['email' => $email]);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        ($this->action)($command);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function invalidInputProvider(): array
    {
        return [
            '無効なメールアドレス' => ['テストユーザー', 'invalid-email', 'Invalid email format: invalid-email'],
            '空の名前' => ['', 'test@example.com', 'Name cannot be empty'],
            '空白のみの名前' => ['   ', 'test@example.com', 'Name cannot be empty'],
            '@なしメール' => ['テストユーザー', 'testexample.com', 'Invalid email format: testexample.com'],
            'ドメインなしメール' => ['テストユーザー', 'test@', 'Invalid email format: test@'],
        ];
    }

    public function test_正常系_開始ログが出力される(): void
    {
        // Arrange
        $command = new CreateUserCommand(
            name: 'ログテストユーザー',
            email: 'logtest@example.com',
            password: 'password123'
        );

        $expectedUser = TestUserFactory::create(
            name: new Name('ログテストユーザー'),
            email: new Email('logtest@example.com')
        );

        $this->userRepository
            ->method('existsByEmail')
            ->willReturn(false);

        $this->userRepository
            ->method('add')
            ->willReturn($expectedUser);

        /** @var array<int, array{message: string, context: array<string, mixed>}> $logMessages */
        $logMessages = [];
        $this->logger
            ->expects($this->any())
            ->method('info')
            ->willReturnCallback(function (string $message, array $context) use (&$logMessages): void {
                $logMessages[] = ['message' => $message, 'context' => $context];
            });

        // Act
        ($this->action)($command);

        // Assert - 最初のログ
        $this->assertEquals('User creation started', $logMessages[0]['message']);
        $this->assertEquals(['email' => 'logtest@example.com'], $logMessages[0]['context']);
    }

    public function test_正常系_成功ログが出力される(): void
    {
        // Arrange
        $command = new CreateUserCommand(
            name: 'ログテストユーザー',
            email: 'logtest@example.com',
            password: 'password123'
        );

        $expectedUser = TestUserFactory::create(
            name: new Name('ログテストユーザー'),
            email: new Email('logtest@example.com')
        );

        $this->userRepository
            ->method('existsByEmail')
            ->willReturn(false);

        $this->userRepository
            ->method('add')
            ->willReturn($expectedUser);

        /** @var array<int, array{message: string, context: array<string, mixed>}> $logMessages */
        $logMessages = [];
        $this->logger
            ->expects($this->any())
            ->method('info')
            ->willReturnCallback(function (string $message, array $context) use (&$logMessages): void {
                $logMessages[] = ['message' => $message, 'context' => $context];
            });

        // Act
        ($this->action)($command);

        // Assert - 2番目のログ
        $this->assertEquals('User created successfully', $logMessages[1]['message']);
        $this->assertEquals($expectedUser->id->toString(), $logMessages[1]['context']['user_id']);
        $this->assertEquals('logtest@example.com', $logMessages[1]['context']['email']);
    }

    /**
     * @throws Exception
     */
    public function test_異常系_リポジトリ例外時のエラーログ(): void
    {
        // Arrange
        $command = new CreateUserCommand(
            name: 'エラーテストユーザー',
            email: 'error@example.com',
            password: 'password123'
        );

        $repositoryException = new RuntimeException('Database connection error');

        $this->userRepository
            ->method('existsByEmail')
            ->willReturn(false);

        $this->userRepository
            ->method('add')
            ->willThrowException($repositoryException);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                'User creation failed - unexpected error',
                $this->callback(function (array $context): bool {
                    return $context['email'] === 'error@example.com' &&
                           $context['error'] === 'Database connection error' &&
                           isset($context['trace']);
                })
            );

        // Act & Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database connection error');

        ($this->action)($command);
    }
}
