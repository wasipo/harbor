<?php

declare(strict_types=1);

namespace App\Domain\Shared\Collections;

use App\Domain\Shared\ValueObjects\AbstractUlidId;
use Closure;
use Countable;
use DomainException;
use Illuminate\Support\Collection;

/**
 * Base class for ULID-based ID collections
 *
 * @template TId of AbstractUlidId
 *
 * Note: hasId() accepts 'mixed' instead of 'TId' because PHP lacks native generics.
 * While @param TId ensures static analysis tools understand the expected type,
 * the runtime cannot enforce this constraint. Using 'mixed' is more honest about
 * PHP's type system limitations.
 */
abstract class UlidIdCollection implements Countable
{
    /** @var Collection<int,TId> */
    private Collection $ids;

    // ===========================================
    // Constructor
    // ===========================================

    /** @param list<TId> $items */
    final public function __construct(array $items = [])
    {
        $behavior = $this->behavior();

        if ($behavior !== CollectionBehavior::ALLOW_DUPLICATES) {
            $items = $this->deduplicate($items, $behavior);
        }

        $this->ids = new Collection($items);
        $this->assertInvariants();
    }

    // ===========================================
    // Static Factory Methods
    // ===========================================

    /** 空コレクション */
    public static function empty(): static
    {
        return new static;
    }

    /**
     * 可変長ファクトリ
     *
     * @param  TId  ...$ids
     */
    public static function of(AbstractUlidId ...$ids): static
    {
        /** @var list<TId> $ids */
        return new static($ids);
    }

    /**
     * 文字列配列からファクトリ
     *
     * @param  array<int, string>  $strings
     */
    public static function fromStrings(array $strings): static
    {
        $stringToId = static::idFactory();
        /** @var list<TId> $ids */
        $ids = array_map($stringToId, $strings);

        return new static($ids);
    }

    // ===========================================
    // Abstract Methods
    // ===========================================

    /**
     * ID生成ファクトリを返す
     *
     * @return Closure(string): TId
     */
    abstract protected static function idFactory(): Closure;

    /**
     * このコレクションの重複制御仕様
     * 具象で必ず指定させる
     */
    abstract protected function behavior(): CollectionBehavior;

    // ===========================================
    // Protected Utility Methods
    // ===========================================

    /**
     * 重複を除去またはエラー処理
     *
     * @param  list<TId>  $items
     * @return list<TId>
     */
    protected function deduplicate(array $items, CollectionBehavior $behavior): array
    {
        /** @var Collection<int,string> $uniq */
        $uniq = collect($items)
            ->unique(fn ($id) => $id->toString())
            ->values();

        // 件数が変わっていれば重複あり
        if ($uniq->count() !== count($items)) {
            if ($behavior === CollectionBehavior::STRICT_NO_DUPLICATES) {
                throw new DomainException('Id が重複しています。');
            }
            // UNIQUE_SILENTの場合は削除済みのまま返す
        }

        /** @var list<TId> */
        return array_values($uniq->all());
    }

    /**
     * 追加的ビジネスルールを子で上書き
     */
    protected function assertInvariants(): void {}

    // ===========================================
    // Public Helper Methods
    // ===========================================

    /**
     * 指定されたIDを含むかチェック
     *
     * @param  TId  $id
     */
    public function hasId(mixed $id): bool
    {
        return $this->ids->contains(fn (mixed $item) => $item->toString() === $id->toString());
    }

    /**
     * 空のコレクションかどうか
     */
    public function isEmpty(): bool
    {
        return $this->ids->isEmpty();
    }

    /**
     * 空でないコレクションかどうか
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * IDの数を返す
     */
    public function count(): int
    {
        return $this->ids->count();
    }

    /**
     * 全要素を返す
     *
     * @return list<TId>
     */
    public function all(): array
    {
        /** @var list<TId> */
        return $this->ids->values()->all();
    }

    /**
     * IDの文字列配列を返す
     *
     * @return list<string>
     */
    public function toStringArray(): array
    {
        /** @var list<string> */
        return $this->ids->map(fn ($id) => $id->toString())->values()->all();
    }

    /**
     * 指定されたIDを含むかチェック（Laravel Collection互換）
     *
     * @param  TId  $id
     */
    public function contains(mixed $id): bool
    {
        return $this->hasId($id);
    }

    /**
     * 各要素に対して処理を実行
     *
     * @param  callable(TId): void  $callback
     */
    public function each(callable $callback): void
    {
        $this->ids->each($callback);
    }

    /**
     * 最初の要素を取得
     *
     * @return TId
     */
    public function first(): ?AbstractUlidId
    {
        return $this->ids->first();
    }
}
