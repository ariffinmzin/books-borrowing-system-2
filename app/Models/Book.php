<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['book_title', 'description', 'author', 'isbn', 'publisher', 'genre', 'photo', 'status'];

    public function borrows(){
        return $this->hasMany(BooksBorrow::class,'book_id');
    }
}
