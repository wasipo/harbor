<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\AccessControl\Category;

use App\Domain\AccessControl\Category\CategoryIdCollection;
use App\Domain\AccessControl\Category\UserCategoryId;
use DomainException;
use Tests\UnitTestCase;

class CategoryIdCollectionTest extends UnitTestCase
{
    public function test_正常系_空コレクション生成(): void
    {
        // Arrange & Act
        $collection = CategoryIdCollection::empty();

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    public function test_正常系_可変長ファクトリで_collection生成(): void
    {
        // Arrange
        $id1 = UserCategoryId::create();
        $id2 = UserCategoryId::create();

        // Act
        $collection = CategoryIdCollection::of($id1, $id2);

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
        $collection = CategoryIdCollection::fromStrings($strings);

        // Assert
        $this->assertCount(2, $collection);
        $this->assertContainsOnlyInstancesOf(UserCategoryId::class, $collection->all());
    }

    public function test_正常系_includingメソッドで_i_d追加(): void
    {
        // Arrange
        $id1 = UserCategoryId::create();
        $id2 = UserCategoryId::create();
        $baseCollection = CategoryIdCollection::of($id1);

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
        $id1 = UserCategoryId::create();
        $id2 = UserCategoryId::create();
        $id3 = UserCategoryId::create();
        $baseCollection = CategoryIdCollection::of($id1);

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
        $id1 = UserCategoryId::create();
        $baseCollection = CategoryIdCollection::of($id1);

        // Act
        $newCollection = $baseCollection->including([]);

        // Assert
        $this->assertCount(1, $newCollection);
        $this->assertTrue($newCollection->contains($id1));
    }

    public function test_異常系_重複_i_dで例外発生(): void
    {
        // Arrange
        $id1 = UserCategoryId::create();
        $id2 = UserCategoryId::create();

        // Act & Assert - 重複があるとDomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Id が重複しています。');

        new CategoryIdCollection([$id1, $id2, $id1]); // id1が重複
    }

    public function test_異常系_無効な_uli_d文字列でfrom_strings(): void
    {
        // Arrange
        $invalidStrings = ['invalid-ulid-string'];

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        CategoryIdCollection::fromStrings($invalidStrings);
    }

    public function test_境界値_単一要素で_collection生成(): void
    {
        // Arrange
        $id = UserCategoryId::create();

        // Act
        $collection = CategoryIdCollection::of($id);

        // Assert
        $this->assertCount(1, $collection);
        $this->assertTrue($collection->contains($id));
    }

    public function test_境界値_大量データで_collection生成(): void
    {
        // Arrange
        $ids = [];
        for ($i = 0; $i < 1000; $i++) {
            $ids[] = UserCategoryId::create();
        }

        // Act
        $collection = CategoryIdCollection::of(...$ids);

        // Assert
        $this->assertCount(1000, $collection);
        foreach ($ids as $id) {
            $this->assertTrue($collection->contains($id));
        }
    }
}
