<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Collections;

use App\Domain\Identity\UserId;
use App\Domain\Shared\Collections\CollectionBehavior;
use App\Domain\Shared\Collections\IdCollection;
use Closure;
use DomainException;
use Tests\UnitTestCase;

// STRICT_NO_DUPLICATES のテスト用
/**
 * @extends IdCollection<UserId>
 */
final class StrictIdCollection extends IdCollection
{
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::STRICT_NO_DUPLICATES;
    }

    protected static function idFactory(): Closure
    {
        return fn (string $s) => UserId::fromString($s);
    }
}

// UNIQUE_SILENT のテスト用
/**
 * @extends IdCollection<UserId>
 */
final class SilentIdCollection extends IdCollection
{
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::UNIQUE_SILENT;
    }

    protected static function idFactory(): Closure
    {
        return fn (string $s) => UserId::fromString($s);
    }
}

// ALLOW_DUPLICATES のテスト用
/**
 * @extends IdCollection<UserId>
 */
final class AllowDuplicatesIdCollection extends IdCollection
{
    protected function behavior(): CollectionBehavior
    {
        return CollectionBehavior::ALLOW_DUPLICATES;
    }

    protected static function idFactory(): Closure
    {
        return fn (string $s) => UserId::fromString($s);
    }
}

class IdCollectionBehaviorTest extends UnitTestCase
{
    public function test_strict_重複idで例外発生(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act & Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Id が重複しています。');

        new StrictIdCollection([$id1, $id2, $id1]); // id1が重複
    }

    public function test_strict_重複なしで正常生成(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act
        $collection = new StrictIdCollection([$id1, $id2]);

        // Assert
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($id1));
        $this->assertTrue($collection->contains($id2));
    }

    public function test_unique_silent_重複idを静かに除去(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act
        $collection = new SilentIdCollection([$id1, $id2, $id1, $id2]); // 重複あり

        // Assert - エラーは発生せず、重複が除去される
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($id1));
        $this->assertTrue($collection->contains($id2));
    }

    public function test_allow_duplicates_重複idをそのまま保持(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act
        $collection = new AllowDuplicatesIdCollection([$id1, $id2, $id1]); // id1が重複

        // Assert - 重複がそのまま保持される
        $this->assertCount(3, $collection);
        // filterメソッドは公開していないので、手動でカウント
        $id1Count = 0;
        foreach ($collection->all() as $id) {
            if ($id->equals($id1)) {
                $id1Count++;
            }
        }
        $this->assertEquals(2, $id1Count);
    }

    public function test_全behavior_from_strings正常動作(): void
    {
        // Arrange
        $strings = ['01ARZ3NDEKTSV4RRFFQ69G5FAV', '01ARZ3NDEKTSV4RRFFQ69G5FAW'];

        // Act
        $strictCollection = StrictIdCollection::fromStrings($strings);
        $silentCollection = SilentIdCollection::fromStrings($strings);
        $allowCollection = AllowDuplicatesIdCollection::fromStrings($strings);

        // Assert
        $this->assertCount(2, $strictCollection);
        $this->assertCount(2, $silentCollection);
        $this->assertCount(2, $allowCollection);
    }

    public function test_strict_from_stringsで重複時例外(): void
    {
        // Arrange
        $ulid = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
        $duplicateStrings = [$ulid, $ulid];

        // Act & Assert - STRICT
        $this->expectException(DomainException::class);
        StrictIdCollection::fromStrings($duplicateStrings);
    }

    public function test_unique_silent_from_stringsで重複除去(): void
    {
        // Arrange
        $ulid = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
        $duplicateStrings = [$ulid, $ulid];

        // Act
        $collection = SilentIdCollection::fromStrings($duplicateStrings);

        // Assert
        $this->assertCount(1, $collection);
    }

    public function test_allow_duplicates_from_stringsで重複保持(): void
    {
        // Arrange
        $ulid = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
        $duplicateStrings = [$ulid, $ulid];

        // Act
        $collection = AllowDuplicatesIdCollection::fromStrings($duplicateStrings);

        // Assert
        $this->assertCount(2, $collection);
    }

    public function test_全behavior_to_string_array正常動作(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();

        // Act
        $collection = new StrictIdCollection([$id1, $id2]);
        $stringArray = $collection->toStringArray();

        // Assert
        $this->assertCount(2, $stringArray);
        $this->assertEquals($id1->toString(), $stringArray[0]);
        $this->assertEquals($id2->toString(), $stringArray[1]);
    }

    public function test_全behavior_has_idで存在確認(): void
    {
        // Arrange
        $id1 = UserId::create();
        $id2 = UserId::create();
        $id3 = UserId::create();

        // Act
        $collection = new StrictIdCollection([$id1, $id2]);

        // Assert
        $this->assertTrue($collection->hasId($id1));
        $this->assertTrue($collection->hasId($id2));
        $this->assertFalse($collection->hasId($id3));
    }

    public function test_全behavior_空判定メソッド動作(): void
    {
        // Arrange & Act
        $emptyCollection = StrictIdCollection::empty();
        $nonEmptyCollection = new StrictIdCollection([UserId::create()]);

        // Assert
        $this->assertTrue($emptyCollection->isEmpty());
        $this->assertFalse($emptyCollection->isNotEmpty());
        $this->assertFalse($nonEmptyCollection->isEmpty());
        $this->assertTrue($nonEmptyCollection->isNotEmpty());
    }
}
