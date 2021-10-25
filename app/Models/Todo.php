<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'url',
        'day',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}