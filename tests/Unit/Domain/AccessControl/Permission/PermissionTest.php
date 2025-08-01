<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AccessControl\Permission;

use App\Domain\AccessControl\Permission\Permission;
use App\Domain\AccessControl\Permission\PermissionId;
use App\Domain\AccessControl\Permission\PermissionKey;
use App\Domain\AccessControl\Permission\PermissionName;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    public function test_正常系_新規作成(): void
    {
        // Arrange & Act
        $permission = Permission::create(
            key: 'posts.create',
            name: '記事作成',
            description: '新規記事を作成する権限'
        );

        // Assert
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertInstanceOf(PermissionId::class, $permission->id);
        $this->assertEquals('posts.create', $permission->key->value());
        $this->assertEquals('posts', $permission->resource);
        $this->assertEquals('create', $permission->action);
        $this->assertEquals('記事作成', $permission->name->value());
        $this->assertEquals('新規記事を作成する権限', $permission->description);
    }

    public function test_正常系_descriptionなしで作成(): void
    {
        // Arrange & Act
        $permission = Permission::create(
            key: 'posts.delete',
            name: '記事削除'
        );

        // Assert
        $this->assertEquals('posts.delete', $permission->key->value());
        $this->assertEquals('posts', $permission->resource);
        $this->assertEquals('delete', $permission->action);
        $this->assertEquals('記事削除', $permission->name->value());
        $this->assertNull($permission->description);
    }

    public function test_正常系_既存IDから作成(): void
    {
        // Arrange
        $id = PermissionId::create();
        
        // Act
        $permission = Permission::reconstitute(
            id: $id,
            key: new PermissionKey('users.edit'),
            resource: 'users',
            action: 'edit',
            name: new PermissionName('ユーザー編集'),
            description: 'ユーザー情報を編集する権限'
        );

        // Assert
        $this->assertSame($id, $permission->id);
        $this->assertEquals('users.edit', $permission->key->value());
        $this->assertEquals('users', $permission->resource);
        $this->assertEquals('edit', $permission->action);
        $this->assertEquals('ユーザー編集', $permission->name->value());
        $this->assertEquals('ユーザー情報を編集する権限', $permission->description);
    }

    public function test_正常系_equals比較(): void
    {
        // Arrange
        $id = PermissionId::create();
        $permission1 = Permission::reconstitute(
            id: $id,
            key: new PermissionKey('posts.create'),
            resource: 'posts',
            action: 'create',
            name: new PermissionName('記事作成')
        );
        $permission2 = Permission::reconstitute(
            id: $id,
            key: new PermissionKey('posts.create'),
            resource: 'posts',
            action: 'create',
            name: new PermissionName('記事作成')
        );
        $permission3 = Permission::create(
            key: 'posts.create',
            name: '記事作成'
        );

        // Act & Assert
        $this->assertTrue($permission1->equals($permission2));
        $this->assertFalse($permission1->equals($permission3));
    }
}