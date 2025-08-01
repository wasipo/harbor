<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\UserCategory
 *
 * @property string $id ULID主キー
 * @property string $code
 * @property string $name
 * @property string $description
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserCategoryAssignment> $assignments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $activeUsers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $assignments_count
 * @property-read int|null $users_count
 * @property-read int|null $active_users_count
 * @property-read int|null $permissions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserCategory where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|UserCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCategory whereIsActive($value)
 * @method static UserCategory|null find($id, $columns = ['*'])
 * @method static UserCategory|null first()
 * @method static \Database\Factories\UserCategoryFactory factory($count = null, $state = [])
 * @method \Illuminate\Database\Eloquent\Builder|UserCategory with($relations)
 */
class UserCategory extends Model
{
    /** @use HasFactory<\Database\Factories\UserCategoryFactory> */
    use HasFactory;

    /**
     * キータイプをstringに設定（ULID使用）
     */
    protected $keyType = 'string';

    /**
     * 自動増分を無効化（ULID使用）
     */
    public $incrementing = false;

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    // Domain behaviors
    public function isAdminCategory(): bool
    {
        return $this->code === 'admin';
    }

    public function isSystemCategory(): bool
    {
        return in_array($this->code, ['admin', 'system'], true);
    }

    public function canBeAssignedToUser(): bool
    {
        return true; // Future: add business rules
    }

    // Relationships
    /**
     * @return HasMany<UserCategoryAssignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(UserCategoryAssignment::class, 'category_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_category_assignments',
            'category_id',
            'user_id'
        )->withPivot('is_primary', 'effective_from', 'effective_until');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('effective_from', '<=', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('user_category_assignments.effective_until')
                    ->orWhere('user_category_assignments.effective_until', '>=', now()->toDateString());
            });
    }

    /**
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'category_permissions',
            'category_id',
            'permission_id'
        )->withTimestamps();
    }
}
