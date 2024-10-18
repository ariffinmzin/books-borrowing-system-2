<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BooksBorrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'borrow_date',
        'return_date',
        'borrow_status',
    ];

    // Define the relationship with the Book model
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the User model
    public function book(){
        return $this->belongsTo(Book::class, 'book_id');
    }
}
