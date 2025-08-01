<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AccessControl\Permission;

use App\Domain\AccessControl\Permission\PermissionName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PermissionNameTest extends TestCase
{
    public function test_正常系_有効な名前で作成(): void
    {
        // Arrange & Act
        $name = new PermissionName('記事作成');

        // Assert
        $this->assertEquals('記事作成', $name->value());
        $this->assertEquals('記事作成', $name->toString());
        $this->assertEquals('記事作成', (string) $name);
    }

    public function test_正常系_英語名で作成(): void
    {
        // Arrange & Act
        $name = new PermissionName('Create Post');

        // Assert
        $this->assertEquals('Create Post', $name->value());
    }

    public function test_正常系_最大文字数で作成(): void
    {
        // Arrange
        $longName = str_repeat('あ', 100);
        
        // Act
        $name = new PermissionName($longName);

        // Assert
        $this->assertEquals($longName, $name->value());
    }

    public function test_異常系_空文字列で例外(): void
    {
        // Arrange & Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission name cannot be empty');

        new PermissionName('');
    }

    public function test_異常系_最大文字数超過で例外(): void
    {
        // Arrange
        $tooLongName = str_repeat('あ', 101);
        
        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Permission name cannot exceed 100 characters');

        new PermissionName($tooLongName);
    }

    public function test_正常系_equals比較(): void
    {
        // Arrange
        $name1 = new PermissionName('記事作成');
        $name2 = new PermissionName('記事作成');
        $name3 = new PermissionName('記事編集');

        // Act & Assert
        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }
}