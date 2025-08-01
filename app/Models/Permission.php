<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id ULID主キー
 * @property string $key
 * @property string $resource
 * @property string $action
 * @property string $display_name
 * @property string|null $description
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserCategory> $categories
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Permission|null find($id, $columns = ['*'])
 * @method static Permission create(array $attributes = [])
 */
class Permission extends Model
{
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
        'key',
        'resource',
        'action',
        'display_name',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            UserCategory::class,
            'category_permissions',
            'permission_id',
            'category_id'
        )->withTimestamps();
    }
}
