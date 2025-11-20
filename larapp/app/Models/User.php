<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'approved',
        'unapproved_by',
        'unapproved_at',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved' => 'boolean',
            'deleted_at' => 'datetime',
            'unapproved_at' => 'datetime',
            'unapproved_by' => 'integer',
        ];
    }

    /**
     * Convenience helpers for role checks
     */
    public function isWebmaster(): bool
    {
        return $this->role === 'webmaster';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canAccessDashboard(): bool
    {
        return $this->approved && ($this->isWebmaster() || $this->isAdmin());
    }

    /**
     * Relation: assigned region roles for this user
     */
    public function regionsRoles()
    {
        return $this->hasMany(UserRegionRole::class, 'user_id');
    }

    /**
     * Return direct assigned region ids for the user optionally filtered by role_key.
     */
    public function getAssignedRegionIds(?string $roleKey = null): array
    {
        $query = $this->regionsRoles();
        if ($roleKey) $query->where('role_key', $roleKey);
        return $query->pluck('region_id')->map(fn($v) => (int)$v)->toArray();
    }

    /**
     * Compute effective region ids (assigned + descendants) optionally filtered by role_key.
     * Results are cached per-user for 5 minutes.
     */
    public function getEffectiveRegionIds(?string $roleKey = null): array
    {
        $cacheKey = "user:{$this->id}:effective_regions:" . ($roleKey ?? 'all');
        return Cache::remember($cacheKey, 300, function() use ($roleKey) {
            $assigned = $this->getAssignedRegionIds($roleKey);
            $all = [];
            foreach ($assigned as $rId) {
                $desc = Regions::collectDescendantIds($rId);
                $all = array_merge($all, $desc);
            }
            return array_values(array_unique($all));
        });
    }
}
