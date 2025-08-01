<?php

declare(strict_types=1);

namespace App\Domain\Shared\Collections;

use Countable;
use DomainException;
use Illuminate\Support\Collection;

/**
 * Base class for entity collections
 * 
 * @template TEntity
 */
abstract class AbstractEntityCollection implements Countable
{
    /** @var Collection<int,TEntity> */
    private Collection $entities;

    /** @param list<TEntity> $items */
    final public function __construct(array $items = [])
    {
        $this->entities = new Collection($this->deduplicate($items));
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
     * @param TEntity ...$entities
     */
    public static function of(...$entities): static
    {
        /** @var list<TEntity> */
        $list = array_values($entities);
        return new static($list);
    }

    // ===========================================
    // Abstract Methods
    // ===========================================
    
    /** 
     * エンティティの重複チェック用の識別子を取得
     * @param TEntity $entity
     */
    abstract protected function getIdentifier($entity): string;

    // ===========================================
    // Protected Methods
    // ===========================================
    
    /** 追加的ビジネスルールを子で上書き */
    protected function assertInvariants(): void {}

    /** 
     * @param list<TEntity> $items 
     * @return list<TEntity>
     */
    private function deduplicate(array $items): array
    {
        $seen = [];
        $result = [];
        
        foreach ($items as $item) {
            $identifier = $this->getIdentifier($item);
            if (isset($seen[$identifier])) {
                throw new DomainException('エンティティが重複しています。');
            }
            $seen[$identifier] = true;
            $result[] = $item;
        }
        
        return $result;
    }

    // ===========================================
    // Public Methods
    // ===========================================
    
    /**
     * 空のコレクションかどうか
     */
    public function isEmpty(): bool
    {
        return $this->entities->isEmpty();
    }

    /**
     * 空でないコレクションかどうか
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
    
    /**
     * エンティティの数を返す
     */
    public function count(): int
    {
        return $this->entities->count();
    }
    
    /**
     * 全要素を返す
     * @return list<TEntity>
     */
    public function all(): array
    {
        /** @var list<TEntity> */
        return $this->entities->values()->all();
    }
    
    /**
     * 指定されたエンティティを含むかチェック
     * @param TEntity $entity
     */
    public function contains($entity): bool
    {
        $identifier = $this->getIdentifier($entity);
        return $this->entities->contains(
            fn ($item) => $this->getIdentifier($item) === $identifier
        );
    }
    
    /**
     * 各要素に対して処理を実行
     * @param callable(TEntity): void $callback
     */
    public function each(callable $callback): void
    {
        $this->entities->each($callback);
    }
}