<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $role_id
 * @property \Carbon\CarbonImmutable $assigned_at
 * @property string|null $assigned_by
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Role $role
 * @property-read User|null $assignedByUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole where($column, $operator = null, $value = null, $boolean = 'and')
 */
class UserRole extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id',
        'user_id',
        'role_id',
        'assigned_at',
        'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    // Domain behaviors
    public function isActive(): bool
    {
        return true; // For now, all role assignments are active
    }

    public function getAssignedByUserId(): ?string
    {
        return $this->assigned_by;
    }

    // Relationships
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
