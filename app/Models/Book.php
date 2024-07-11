<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'book_url',
    ];

    /**
     * The attributes that should be guarded for serialization.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'ratings',
    ];

    protected $casts = [
        'ratings' => 'array'
    ];

    public static $searchable = [
        'title',
        'name',
    ];

    public function authors() {
        return $this->belongsToMany(Author::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    protected function rating(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => collect($this->ratings)->avg('rating'),
        );
    }
}
