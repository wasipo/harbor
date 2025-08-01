<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AccessControl\Permission;

use App\Domain\AccessControl\Permission\PermissionKey;
use InvalidArgumentException;
use Tests\UnitTestCase;

class PermissionKeyTest extends UnitTestCase
{
    public function test_正常系_有効なキーで作成(): void
    {
        // Arrange & Act
        $key = new PermissionKey('posts.create');

        // Assert
        $this->assertEquals('posts.create', $key->value());
        $this->assertEquals('posts.create', $key->toString());
        $this->assertEquals('posts.create', (string) $key);
    }

    public function test_正常系_単一セグメントのキー(): void
    {
        // Arrange & Act
        $key = new PermissionKey('admin');

        // Assert
        $this->assertEquals('admin', $key->value());
    }

    public function test_正常系_複数セグメントのキー(): void
    {
        // Arrange & Act
        $key = new PermissionKey('posts.comments.create');

        // Assert
        $this->assertEquals('posts.comments.create', $key->value());
    }

    public function test_異常系_空文字列で例外(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission key cannot be empty');

        new PermissionKey('');
    }

    public function test_異常系_大文字を含むキーで例外(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission key must be lowercase letters separated by dots');

        new PermissionKey('Posts.create');
    }

    public function test_異常系_数字を含むキーで例外(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission key must be lowercase letters separated by dots');

        new PermissionKey('posts.create2');
    }

    public function test_異常系_ハイフンを含むキーで例外(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission key must be lowercase letters separated by dots');

        new PermissionKey('posts-create');
    }

    public function test_異常系_連続ドットで例外(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission key must be lowercase letters separated by dots');

        new PermissionKey('posts..create');
    }

    public function test_正常系_equals比較(): void
    {
        // Arrange
        $key1 = new PermissionKey('posts.create');
        $key2 = new PermissionKey('posts.create');
        $key3 = new PermissionKey('posts.edit');

        // Act & Assert
        $this->assertTrue($key1->equals($key2));
        $this->assertFalse($key1->equals($key3));
    }
}
