<?php

namespace Tests\Unit\Application\Identity\Commands;

use App\Adapter\Identity\CreateUserCommand;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function test_正常系_コマンド作成成功()
    {
        // Arrange & Act
        $command = new CreateUserCommand(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123',
            isActive: true,
            categoryIds: ['01ARZ3NDEKTSV4RRFFQ69G5FAV', '01ARZ3NDEKTSV4RRFFQ69G5FAW'],
            roleIds: ['01ARZ3NDEKTSV4RRFFQ69G5FAX']
        );

        // Assert
        $this->assertEquals('Test User', $command->name);
        $this->assertEquals('test@example.com', $command->email);
        $this->assertEquals('password123', $command->password);
        $this->assertTrue($command->isActive);
        $this->assertIsArray($command->categoryIds);
        $this->assertIsArray($command->roleIds);
        $this->assertCount(2, $command->categoryIds);
        $this->assertCount(1, $command->roleIds);
    }

    public function test_正常系_空配列でコマンド作成()
    {
        // Arrange & Act
        $command = new CreateUserCommand(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123',
            isActive: true,
            categoryIds: [],
            roleIds: []
        );

        // Assert
        $this->assertEquals('Test User', $command->name);
        $this->assertEquals('test@example.com', $command->email);
        $this->assertEquals('password123', $command->password);
        $this->assertTrue($command->isActive);
        $this->assertIsArray($command->categoryIds);
        $this->assertIsArray($command->roleIds);
        $this->assertEmpty($command->categoryIds);
        $this->assertEmpty($command->roleIds);
    }

    public function test_正常系_is_activeがfalseの場合()
    {
        // Arrange & Act
        $command = new CreateUserCommand(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123',
            isActive: false,
            categoryIds: [],
            roleIds: []
        );

        // Assert
        $this->assertEquals('Test User', $command->name);
        $this->assertEquals('test@example.com', $command->email);
        $this->assertEquals('password123', $command->password);
        $this->assertFalse($command->isActive);
        $this->assertIsArray($command->categoryIds);
        $this->assertIsArray($command->roleIds);
    }

    public function test_正常系_複数カテゴリとロール()
    {
        // Arrange & Act
        $categoryIds = [
            '01ARZ3NDEKTSV4RRFFQ69G5FAV',
            '01ARZ3NDEKTSV4RRFFQ69G5FAW',
            '01ARZ3NDEKTSV4RRFFQ69G5FAX',
        ];
        $roleIds = [
            '01ARZ3NDEKTSV4RRFFQ69G5FAY',
            '01ARZ3NDEKTSV4RRFFQ69G5FAZ',
        ];

        $command = new CreateUserCommand(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123',
            isActive: true,
            categoryIds: $categoryIds,
            roleIds: $roleIds
        );

        // Assert
        $this->assertEquals($categoryIds, $command->categoryIds);
        $this->assertEquals($roleIds, $command->roleIds);
        $this->assertCount(3, $command->categoryIds);
        $this->assertCount(2, $command->roleIds);
    }

    public function test_正常系_パスワードが平文で保持される()
    {
        // Arrange & Act
        $command = new CreateUserCommand(
            name: 'Test User',
            email: 'test@example.com',
            password: 'plaintext123',
            isActive: true,
            categoryIds: [],
            roleIds: []
        );

        // Assert - パスワードが平文のまま保持される（ドメインに漏れない）
        $this->assertEquals('plaintext123', $command->password);
        $this->assertStringNotContainsString('$2y$', $command->password); // bcrypt形式でない
    }
}
