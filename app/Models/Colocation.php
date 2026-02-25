<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
protected $fillable = ['name', 'status'];

public function memberships() {
    return $this->hasMany(Membership::class);
}

public function activeMembers() {
    return $this->hasMany(Membership::class)->whereNull('left_at');
}

public function owner() {
    return $this->hasOne(Membership::class)->where('role', 'owner')->whereNull('left_at');
}

public function expenses() {
    return $this->hasMany(Expense::class);
}

public function invitations() {
    return $this->hasMany(Invitation::class);
}
}
