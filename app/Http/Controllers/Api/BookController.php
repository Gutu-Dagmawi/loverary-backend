<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        $books = Book::with('bookCopies')->get();

        return response()->json([
            'success' => true,
            'data' => $books,
        ]);
    }

    public function show(Book $book): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $book->load('bookCopies'),
        ]);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $bookCopiesData = $validatedData['book_copies'] ?? [];
        $bookData = Arr::except($validatedData, ['book_copies']);

        DB::beginTransaction();

        try {
            $book = Book::create($bookData);

            foreach ($bookCopiesData as $copyDatum) {
                $book->bookCopies()->create($copyDatum);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Book and copies created successfully.',
                'data' => $book->load('bookCopies')
            ], 201);

        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create book and copies.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Book $book, UpdateBookRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $bookData = Arr::except($validatedData, ['book_copies']);
        $bookCopiesData = $validatedData['book_copies'] ?? [];

        DB::beginTransaction();

        try {
            $book->update($bookData);

            foreach ($bookCopiesData as $copyDatum) {
                $bookCopy = BookCopy::where('barcode', $copyDatum['barcode'])->firstOrFail();
                $bookCopy->update(Arr::except($copyDatum, ['barcode']));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Book updated successfully.',
                'data' => $book->fresh()->load('bookCopies'),
            ], 200);

        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update book.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(Book $book): JsonResponse
    {
        DB::beginTransaction();

        try {
            $book->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Book and its copies deleted successfully.',
            ]);

        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete book.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
