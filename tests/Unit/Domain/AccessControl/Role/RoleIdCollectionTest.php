<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AccessControl\Role;

use App\Domain\AccessControl\Role\RoleId;
use App\Domain\AccessControl\Role\RoleIdCollection;
use DomainException;
use Tests\UnitTestCase;

class RoleIdCollectionTest extends UnitTestCase
{
    public function test_正常系_空コレクション生成(): void
    {
        // Arrange & Act
        $collection = RoleIdCollection::empty();

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    public function test_正常系_可変長ファクトリで_collection生成(): void
    {
        // Arrange
        $id1 = RoleId::create();
        $id2 = RoleId::create();

        // Act
        $collection = RoleIdCollection::of($id1, $id2);

        // Assert
        $this->assertFalse($collection->isEmpty());
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($id1));
        $this->assertTrue($collection->contains($id2));
    }

    public function test_正常系_文字列配列から_collection生成(): void
    {
        // Arrange
        $strings = ['01ARZ3NDEKTSV4RRFFQ69G5FAV', '01ARZ3NDEKTSV4RRFFQ69G5FAW'];

        // Act
        $collection = RoleIdCollection::fromStrings($strings);

        // Assert
        $this->assertCount(2, $collection);
        $this->assertContainsOnlyInstancesOf(RoleId::class, $collection->all());
    }

    public function test_正常系_includingメソッドで_i_d追加(): void
    {
        // Arrange
        $id1 = RoleId::create();
        $id2 = RoleId::create();
        $baseCollection = RoleIdCollection::of($id1);

        // Act
        $newCollection = $baseCollection->including([$id2->toString()]);

        // Assert
        $this->assertCount(1, $baseCollection); // 元のコレクションは変更されない
        $this->assertCount(2, $newCollection); // 新しいコレクションは2つ
        $this->assertTrue($newCollection->contains($id1));
        $this->assertTrue($newCollection->contains($id2));
    }

    public function test_正常系_複数_i_d同時追加(): void
    {
        // Arrange
        $id1 = RoleId::create();
        $id2 = RoleId::create();
        $id3 = RoleId::create();
        $baseCollection = RoleIdCollection::of($id1);

        // Act
        $newCollection = $baseCollection->including([$id2->toString(), $id3->toString()]);

        // Assert
        $this->assertCount(3, $newCollection);
        $this->assertTrue($newCollection->contains($id1));
        $this->assertTrue($newCollection->contains($id2));
        $this->assertTrue($newCollection->contains($id3));
    }

    public function test_正常系_空配列でincluding(): void
    {
        // Arrange
        $id1 = RoleId::create();
        $baseCollection = RoleIdCollection::of($id1);

        // Act
        $newCollection = $baseCollection->including([]);

        // Assert
        $this->assertCount(1, $newCollection);
        $this->assertTrue($newCollection->contains($id1));
    }

    public function test_異常系_重複_i_dで例外発生(): void
    {
        // Arrange
        $id1 = RoleId::create();
        $id2 = RoleId::create();

        // Act & Assert - 重複があるとDomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Id が重複しています。');

        new RoleIdCollection([$id1, $id2, $id1]); // id1が重複
    }

    public function test_異常系_無効な_uli_d文字列でfrom_strings(): void
    {
        // Arrange
        $invalidStrings = ['invalid-ulid-string'];

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        RoleIdCollection::fromStrings($invalidStrings);
    }

    public function test_境界値_単一要素で_collection生成(): void
    {
        // Arrange
        $id = RoleId::create();

        // Act
        $collection = RoleIdCollection::of($id);

        // Assert
        $this->assertCount(1, $collection);
        $this->assertTrue($collection->contains($id));
    }

    public function test_境界値_大量データで_collection生成(): void
    {
        // Arrange
        $ids = [];
        for ($i = 0; $i < 10; $i++) {
            $ids[] = RoleId::create();
        }

        // Act
        $collection = RoleIdCollection::of(...$ids);

        // Assert
        $this->assertCount(10, $collection);
        foreach ($ids as $id) {
            $this->assertTrue($collection->contains($id));
        }
    }

    public function test_正常系_イミュータブル性確認(): void
    {
        // Arrange
        $id1 = RoleId::create();
        $id2 = RoleId::create();
        $originalCollection = RoleIdCollection::of($id1);

        // Act
        $newCollection = $originalCollection->including([$id2->toString()]);

        // Assert - 元のコレクションは変更されない
        $this->assertCount(1, $originalCollection);
        $this->assertCount(2, $newCollection);
        $this->assertNotSame($originalCollection, $newCollection);
    }
}
