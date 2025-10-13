<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    /* Role Definition */
    const ROLE_ADMIN = 0;
    const ROLE_MGR = 1;
    const ROLE_USER = 2;
    const ROLE_VIEWER = 3;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /* Ask for admin */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    public function isManager(): bool
    {
        return $this->role === self::ROLE_MGR;
    }

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
        ];
    }
}
