<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Collections;

use App\Domain\Identity\UserId;
use App\Domain\Shared\Collections\CollectionBehavior;
use App\Domain\Shared\Collections\IdCollection;
use Closure;
use DomainException;
use Tests\UnitTestCase;

// テスト用の具象クラス
/**
 * @extends IdCollection<UserId>
 */
final class TestIdCollection extends IdCollection
{
    /**
     * テスト用: 重複を許可しない
     */
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::STRICT_NO_DUPLICATES;
    }

    /**
     * @return Closure(string): UserId
     */
    protected static function idFactory(): Closure
    {
        return fn (string $s) => UserId::fromString($s);
    }
}

class IdCollectionTest extends UnitTestCase
{
    public function test_正常系_空コレクション生成(): void
    {
        // Arrange & Act
        $collection = TestIdCollection::empty();

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    public function test_正常系_可変長ファクトリで_collection生成(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act
        $collection = TestIdCollection::of($id1, $id2);

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
        $collection = TestIdCollection::fromStrings($strings);

        // Assert
        $this->assertCount(2, $collection);
        $this->assertContainsOnlyInstancesOf(UserId::class, $collection->all());
    }

    public function test_正常系_重複排除機能(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act & Assert - 重複があるとDomainException
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Id が重複しています。');

        new TestIdCollection([$id1, $id2, $id1]); // id1が重複
    }

    public function test_正常系_containsメソッド(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();
        $id3 = UserId::create();
        $collection = TestIdCollection::of($id1, $id2);

        // Act & Assert
        $this->assertTrue($collection->contains($id1));
        $this->assertTrue($collection->contains($id2));
        $this->assertFalse($collection->contains($id3));
    }

    public function test_異常系_無効な_uli_d文字列でfrom_strings(): void
    {
        // Arrange
        $strings = ['invalid-string'];

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ULID');
        TestIdCollection::fromStrings($strings);
    }

    public function test_境界値_空配列でfrom_strings(): void
    {
        // Arrange
        $strings = [];

        // Act
        $collection = TestIdCollection::fromStrings($strings);

        // Assert
        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
    }

    public function test_境界値_単一要素で_collection生成(): void
    {
        // Arrange
        $id = UserId::create();

        // Act
        $collection = TestIdCollection::of($id);

        // Assert
        $this->assertCount(1, $collection);
        $this->assertTrue($collection->contains($id));
    }
}
