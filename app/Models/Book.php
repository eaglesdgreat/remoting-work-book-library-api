<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'author_id',
        'image_url',
        'number_of_pages',
        'publisher',
        'published_date',
        'language',
        'ratings',
        'book_url',
    ];

    protected $casts = [
        'ratings' => 'array'
    ];

    public function author() {
        return $this->belongsTo(Author::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
