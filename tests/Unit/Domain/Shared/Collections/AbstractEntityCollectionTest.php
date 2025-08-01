<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Collections;

use App\Domain\Shared\Collections\AbstractEntityCollection;
use DomainException;
use PHPUnit\Framework\TestCase;

// テスト用のエンティティ
class TestEntity
{
    public function __construct(
        public readonly string $id,
        public readonly string $name
    ) {}
}

// テスト用のコレクション
class TestEntityCollection extends AbstractEntityCollection
{
    protected function getIdentifier($entity): string
    {
        return $entity->id;
    }
}

class AbstractEntityCollectionTest extends TestCase
{
    public function test_正常系_空コレクション生成(): void
    {
        // Arrange & Act
        $collection = TestEntityCollection::empty();

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    public function test_正常系_可変長ファクトリでcollection生成(): void
    {
        // Arrange
        $entity1 = new TestEntity('1', 'Entity 1');
        $entity2 = new TestEntity('2', 'Entity 2');

        // Act
        $collection = TestEntityCollection::of($entity1, $entity2);

        // Assert
        $this->assertFalse($collection->isEmpty());
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($entity1));
        $this->assertTrue($collection->contains($entity2));
    }

    public function test_正常系_配列からcollection生成(): void
    {
        // Arrange
        $entities = [
            new TestEntity('1', 'Entity 1'),
            new TestEntity('2', 'Entity 2'),
            new TestEntity('3', 'Entity 3')
        ];

        // Act
        $collection = new TestEntityCollection($entities);

        // Assert
        $this->assertCount(3, $collection);
        foreach ($entities as $entity) {
            $this->assertTrue($collection->contains($entity));
        }
    }

    public function test_異常系_重複エンティティで例外発生(): void
    {
        // Arrange
        $entity1 = new TestEntity('1', 'Entity 1');
        $entity2 = new TestEntity('2', 'Entity 2');
        $entity1Dup = new TestEntity('1', 'Entity 1 Duplicate'); // 同じID

        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('エンティティが重複しています。');

        new TestEntityCollection([$entity1, $entity2, $entity1Dup]);
    }

    public function test_正常系_containsメソッド(): void
    {
        // Arrange
        $entity1 = new TestEntity('1', 'Entity 1');
        $entity2 = new TestEntity('2', 'Entity 2');
        $entity3 = new TestEntity('3', 'Entity 3');
        $collection = TestEntityCollection::of($entity1, $entity2);

        // Act & Assert
        $this->assertTrue($collection->contains($entity1));
        $this->assertTrue($collection->contains($entity2));
        $this->assertFalse($collection->contains($entity3));
    }

    public function test_正常系_eachメソッド(): void
    {
        // Arrange
        $entity1 = new TestEntity('1', 'Entity 1');
        $entity2 = new TestEntity('2', 'Entity 2');
        $collection = TestEntityCollection::of($entity1, $entity2);
        $names = [];

        // Act
        $collection->each(function (TestEntity $entity) use (&$names) {
            $names[] = $entity->name;
        });

        // Assert
        $this->assertEquals(['Entity 1', 'Entity 2'], $names);
    }

    public function test_正常系_allメソッド(): void
    {
        // Arrange
        $entity1 = new TestEntity('1', 'Entity 1');
        $entity2 = new TestEntity('2', 'Entity 2');
        $collection = TestEntityCollection::of($entity1, $entity2);

        // Act
        $all = $collection->all();

        // Assert
        $this->assertCount(2, $all);
        $this->assertSame($entity1, $all[0]);
        $this->assertSame($entity2, $all[1]);
    }

    public function test_境界値_空配列でcollection生成(): void
    {
        // Arrange & Act
        $collection = new TestEntityCollection([]);

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    public function test_境界値_単一要素でcollection生成(): void
    {
        // Arrange
        $entity = new TestEntity('1', 'Entity 1');

        // Act
        $collection = TestEntityCollection::of($entity);

        // Assert
        $this->assertCount(1, $collection);
        $this->assertTrue($collection->contains($entity));
    }
}