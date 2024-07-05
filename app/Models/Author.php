<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'about',
        'summary',
        'dateBirthed',
        'dateDied'
    ];

    public function books() {
        $this->hasMany(Book::class);
    }
}