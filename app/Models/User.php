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
    
    protected $fillable = [
    'name', 'email', 'password',
    'is_admin', 'is_banned', 'reputation',
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
    protected function casts(): array{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
    ];
}
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Relations
public function memberships() {
    return $this->hasMany(Membership::class);
}

public function activeMembership() {
    return $this->hasOne(Membership::class)->whereNull('left_at');
}

public function expenses() {
    return $this->hasMany(Expense::class, 'paid_by');
}
}
