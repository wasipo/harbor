<?php

namespace Tests\Unit\Domain\Identity\ValueObjects;

use App\Domain\Identity\UserId;
use InvalidArgumentException;
use Tests\UnitTestCase;

class UserIdTest extends UnitTestCase
{
    public function test_正常系_uli_d生成(): void
    {
        // Arrange & Act
        $userId = UserId::create();

        // Assert
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals(26, strlen($userId->toString()));
    }

    public function test_正常系_文字列から作成(): void
    {
        // Arrange
        $validUlid = '01K06EWBM2JK3M31SE7NWJ50GW';

        // Act
        $userId = UserId::fromString($validUlid);

        // Assert
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals($validUlid, $userId->toString());
    }

    public function test_異常系_空文字列拒否(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        UserId::fromString('');
    }

    public function test_異常系_ゼロ文字列拒否(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        UserId::fromString('0');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidUlidProvider')]
    public function test_異常系_不正な_uli_d形式拒否(string $invalidUlid, string $expectedMessage): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        UserId::fromString($invalidUlid);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidUlidProvider(): array
    {
        return [
            '25文字' => ['01HZKT234567890ABCDEFGHIJ', 'Invalid ULID'],
            '27文字' => ['01HZKT234567890ABCDEFGHIJKL', 'Invalid ULID'],
            '不正文字' => ['01HZKT234567890ABCDEFGHIO', 'Invalid ULID'], // OはULIDで使用不可
            '小文字' => ['01hzkt234567890abcdefghijk', 'Invalid ULID'],
            '記号含む' => ['01HZKT234567890ABCDEFGHI!', 'Invalid ULID'],
        ];
    }

    public function test_正常系_equals比較(): void
    {
        // Arrange
        $ulid = '01K06EWBM2JK3M31SE7NWJ50GW';
        $userId1 = UserId::fromString($ulid);
        $userId2 = UserId::fromString($ulid);
        $userId3 = UserId::create();

        // Act & Assert
        $this->assertTrue($userId1->equals($userId2));
        $this->assertFalse($userId1->equals($userId3));
    }

    public function test_正常系_to_string変換(): void
    {
        // Arrange
        $ulid = '01K06EWBM2JK3M31SE7NWJ50GX';
        $userId = UserId::fromString($ulid);

        // Act
        $result = $userId->toString();

        // Assert
        $this->assertEquals($ulid, $result);
    }
}
