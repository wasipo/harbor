<?php

namespace Tests\Unit\Domain\Identity\User;

use App\Domain\Identity\Email;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\UnitTestCase;

class EmailTest extends UnitTestCase
{
    public function test_正常系_有効なメールアドレス受入(): void
    {
        // Arrange
        $validEmail = 'test@example.com';

        // Act
        $email = new Email($validEmail);

        // Assert
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals($validEmail, $email->value);
    }

    #[DataProvider('invalidEmailProvider')]
    public function test_異常系_無効なメールアドレス拒否(string $invalidEmail, string $expectedMessage): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        new Email($invalidEmail);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidEmailProvider(): array
    {
        return [
            'アットマーク無し' => ['invalid-email', 'Invalid email format: invalid-email'],
            'ドメイン無し' => ['test@', 'Invalid email format: test@'],
            'ローカル部無し' => ['@example.com', 'Invalid email format: @example.com'],
            'ドット連続' => ['test..test@example.com', 'Invalid email format: test..test@example.com'],
            '空文字' => ['', 'Invalid email format: '],
            'スペース含む' => ['test @example.com', 'Invalid email format: test @example.com'],
        ];
    }

    public function test_正常系_日本語ドメイン対応(): void
    {
        // Arrange
        $japaneseEmail = 'test@example.jp';

        // Act
        $email = new Email($japaneseEmail);

        // Assert
        $this->assertEquals($japaneseEmail, $email->value);
    }

    #[DataProvider('validEmailProvider')]
    public function test_正常系_様々な有効メールアドレス(string $validEmail): void
    {
        // Arrange & Act
        $email = new Email($validEmail);

        // Assert
        $this->assertEquals($validEmail, $email->value);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validEmailProvider(): array
    {
        return [
            '通常形式' => ['user@example.com'],
            'サブドメイン' => ['user@mail.example.com'],
            'ピリオド含む' => ['user.name@example.com'],
            'プラス含む' => ['user+tag@example.com'],
            '数字含む' => ['user123@example.com'],
            'ハイフン含む' => ['user-name@example.com'],
        ];
    }
}
