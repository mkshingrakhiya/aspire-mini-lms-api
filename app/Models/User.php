<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Filter users by given role.
     *
     * @param Builder $query
     * @param UserRole $role
     * @return Builder
     */
    public function scopeWithRole(Builder $query, UserRole $role): Builder
    {
        return $query->whereRole($role);
    }

    /**
     * Determine whether the user is a client.
     *
     * @return Attribute
     */
    public function isClient(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => ($attributes['role'] === UserRole::CLIENT->value)
        );
    }
    
    /**
     * Determine whether the user is a reviewer.
     *
     * @return Attribute
     */
    public function isReviewer(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => ($attributes['role'] === UserRole::REVIEWER->value)
        );
    }
    
    /**
     * Hash the password.
     *
     * @return Attribute
     */
    public function password(): Attribute
    {
        return new Attribute(
            set: fn ($value) => Hash::make($value)
        );
    }

    /**
     * Get all of the loans for the User
     *
     * @return HasMany
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, match ($this->role) {
            UserRole::CLIENT->value => 'client_id',
            UserRole::REVIEWER->value => 'reviewer_id'
        });
    }
}
