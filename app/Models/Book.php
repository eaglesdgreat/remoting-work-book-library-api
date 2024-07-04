<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ratings' => 'array'
    ];

    public function author() {
        $this->belongsTo(Author::class);
    }

    public function reviews() {
        $this->hasMany(Review::class);
    }
}
