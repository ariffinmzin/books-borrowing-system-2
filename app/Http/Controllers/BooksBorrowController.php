<?php

namespace App\Http\Controllers;

use App\Models\BooksBorrow;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class BooksBorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $borrowRecords = collect();  // Default to an empty collection
        $availableBooks = collect(); // Default to an empty collection

        if (auth()->user()->role === 'admin') {
            // Admins see only borrow records where books are reserved
            $borrowRecords = BooksBorrow::with('user', 'book')
                ->whereHas('book', function ($query) {
                    $query->where('status', 'reserved');
                })->get();
        // dd($borrowRecords); 
        } else {
            // Users see only books with available status
            $availableBooks = Book::where('status', 'available')->get();
        }

        // Pass both variables to the view
        return view('booksborrow.index', compact('borrowRecords', 'availableBooks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $booksBorrow = BooksBorrow::with('book')->where('user_id', $id)->get();

        if ($booksBorrow->isEmpty()) {
            return view('booksborrow.show', compact('booksBorrow'))->with('message', 'No borrowing records found.');
        }

        return view('booksborrow.show', compact('booksBorrow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BooksBorrow $booksBorrow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        // Find the borrowing record or fail
        $booksBorrow = BooksBorrow::findOrFail($id);

        // Validate the request
        $validatedData = $request->validate([
            'borrow_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after_or_equal:borrow_date',
            'borrow_status' => 'required|string|in:borrowed,returned,overdue'
        ], [
            'borrow_date.after_or_equal' => 'The borrow date must be today or a future date.',
            'return_date.after_or_equal' => 'The return date must be after or the same as the borrow date.',
        ]);

        // Update the borrowing record
        $booksBorrow->update($validatedData);

        // If the borrow status is 'returned', update the book status to 'available'
        if ($validatedData['borrow_status'] === 'returned') {
            $book = $booksBorrow->book;  // Access the related book model
            $book->update(['status' => 'available']);
        }

        // Redirect back with a success message
        return redirect()->back()->with('message', 'Borrowing record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BooksBorrow $booksBorrow)
    {
        //
    }

    public function reserve(Request $request, Book $book)
    {
        //
        // Validate the request
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'status' => 'required|string',
        ]);

        // Update the book status to 'reserved'
        $book->update(['status' => $validatedData['status']]);

        // Insert a new record into the books_borrows table
        BooksBorrow::create([
            'book_id' => $validatedData['book_id'],
            'user_id' => $validatedData['user_id'],
        ]);

        // Redirect with success message
        return redirect()->back()->with('message', 'The book has been reserved!');
    }
}
