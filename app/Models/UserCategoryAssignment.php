<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Infrastructure\Persistence\Eloquent\UserCategoryAssignment
 *
 * @property-read User $user
 * @property-read UserCategory $category
 * 
 * @method static bool fillAndInsert(array $rows)
 */
class UserCategoryAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'is_primary',
        'effective_from',
        'effective_until',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'effective_from' => 'date',
            'effective_until' => 'date',
        ];
    }

    // Domain behaviors
    public function isActive(?string $date = null): bool
    {
        $checkDate = $date ?? now()->toDateString();

        return $this->effective_from <= $checkDate &&
               (is_null($this->effective_until) || $this->effective_until >= $checkDate);
    }

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function expire(?string $endDate = null): void
    {
        $this->effective_until = $endDate ?? now()->toDateString();
    }

    public function extend(string $newEndDate): void
    {
        $this->effective_until = $newEndDate;
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(UserCategory::class, 'category_id');
    }
}
