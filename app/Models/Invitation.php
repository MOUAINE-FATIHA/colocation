<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'colocation_id',
        'email',
        'token',
        'status', // pending, accepted, refused
    ];

    // Relations
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}