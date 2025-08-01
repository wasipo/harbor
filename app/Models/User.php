<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property string $id ULID主キー
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserCategory> $activeCategories
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserCategoryAssignment> $categoryAssignments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserRole> $userRoles
 * @property-read int|null $roles_count
 * @property-read int|null $active_categories_count
 * @property-read int|null $category_assignments_count
 * @property-read int|null $user_roles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNull($column)
 * @method static \Illuminate\Database\Eloquent\Builder|User find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|User first($columns = ['*'])
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotNull($column)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User findOrFail($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|User firstOrFail($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|User lockForUpdate()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * キータイプをstringに設定（ULID使用）
     */
    protected $keyType = 'string';

    /**
     * 自動増分を無効化（ULID使用）
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'immutable_datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Domain behaviors
    public function activate(): void
    {
        $this->is_active = true;
    }

    public function deactivate(): void
    {
        $this->is_active = false;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    // Authorization methods
    public function isAdmin(): bool
    {
        return $this->activeCategories->contains('code', 'admin');
    }

    // Relationships to other domains
    /**
     * @return HasMany<UserCategoryAssignment, $this>
     */
    public function categoryAssignments(): HasMany
    {
        return $this->hasMany(UserCategoryAssignment::class);
    }

    /**
     * @return BelongsToMany<UserCategory, $this>
     */
    public function activeCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            UserCategory::class,
            'user_category_assignments',
            'user_id',
            'category_id'
        )->withPivot('is_primary', 'effective_from', 'effective_until')
            ->wherePivot('effective_from', '<=', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('user_category_assignments.effective_until')
                    ->orWhere('user_category_assignments.effective_until', '>=', now()->toDateString());
            });
    }

    /**
     * @return HasMany<UserRole, $this>
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'user_roles',
            'user_id',
            'role_id'
        )->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }
}
