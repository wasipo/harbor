<?php

namespace App\Models;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserRole> $userRoles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $user_roles_count
 * @property-read int|null $users_count
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role create(array $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Role updateOrCreate(array $attributes, array $values = [])
 * @method static Role|null find($id, $columns = ['*'])
 * @method static Role|null first()
 * @method static \Illuminate\Database\Query\Builder all()
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 */
class Role extends Model
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
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

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
