<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'is_read'];

    public function user() {
        $this->belongsTo(User::class);
    }
}