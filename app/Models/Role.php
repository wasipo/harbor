<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Role
 *
 * @property string $id ULID主キー
 * @property string $name
 * @property string $display_name
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserRole> $userRoles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $user_roles_count
 * @property-read int|null $users_count
 * @property-read int|null $permissions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Role where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDisplayName($value)
 * @method static Role|null find($id, $columns = ['*'])
 * @method static Role|null first()
 * @method static \Illuminate\Database\Eloquent\Collection<int, static> all($columns = ['*'])
 * @method static RoleFactory factory($count = null, $state = [])
 */
class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
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
        'name',
        'display_name',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    // Domain behaviors
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('key', $permission);
    }

    // Relationships
    /**
     * @return HasMany<UserRole, $this>
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id'
        )->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }
}
