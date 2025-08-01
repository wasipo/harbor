<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AccessControl\Permission;

use App\Domain\AccessControl\Permission\Permission;
use App\Domain\AccessControl\Permission\PermissionCollection;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use Tests\UnitTestCase;

class PermissionCollectionTest extends UnitTestCase
{
    #[Test]
    public function 正常系_空コレクション生成(): void
    {
        // Arrange & Act
        $collection = PermissionCollection::empty();

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    #[Test]
    public function 正常系_可変長ファクトリでcollection生成(): void
    {
        // Arrange
        $permission1 = Permission::create('posts.create', 'posts', '記事作成');
        $permission2 = Permission::create('posts.edit', 'posts', '記事編集');

        // Act
        $collection = PermissionCollection::of($permission1, $permission2);

        // Assert
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($permission1));
        $this->assertTrue($collection->contains($permission2));
    }

    #[Test]
    public function 異常系_重複権限で例外発生(): void
    {
        // Arrange
        $permission1 = Permission::create('posts.create', 'posts', '記事作成');
        $permission2 = Permission::create('posts.edit', 'posts', '記事編集');

        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('エンティティが重複しています。');

        new PermissionCollection([$permission1, $permission2, $permission1]);
    }

    #[Test]
    public function 正常系_to_idsメソッド(): void
    {
        // Arrange
        $permission1 = Permission::create('posts.create', 'posts', '記事作成');
        $permission2 = Permission::create('posts.edit', 'posts', '記事編集');
        $collection = PermissionCollection::of($permission1, $permission2);

        // Act
        $idCollection = $collection->toIds();

        // Assert
        $this->assertEquals(2, $idCollection->count());
        $this->assertTrue($idCollection->hasId($permission1->id));
        $this->assertTrue($idCollection->hasId($permission2->id));
    }
}
