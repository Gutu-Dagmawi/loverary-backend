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
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class BookController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        $pageSize = intval(request()->get('per_page', 10));
        $pageSize = max(1, min($pageSize, 100));

        $paginated = Book::with('bookCopies')->paginate($pageSize);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'has_more' => $paginated->hasMorePages(),
            ],
            'total' => Book::count(),
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
        $this->abortUnlessAdmin();

        $validatedData = $request->validated();
        $bookCopiesData = $validatedData['book_copies'] ?? [];
        $bookData = Arr::except($validatedData, ['book_copies']);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $bookData['cover_image'] = $path;
        }

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

            // Optional: delete uploaded image if the DB operation failed
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create book and copies.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function update(Book $book, UpdateBookRequest $request): JsonResponse
    {
        $this->abortUnlessAdmin();

        $validatedData = $request->validated();
        $bookCopiesData = $validatedData['book_copies'] ?? [];
        $bookData = Arr::except($validatedData, ['book_copies']);

        // Handle new cover image upload if provided
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // Store new image
            $path = $request->file('cover_image')->store('covers', 'public');
            $bookData['cover_image'] = $path;
        }

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

            // Delete newly uploaded image if DB update fails
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update book.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function destroy(Book $book): JsonResponse
    {
        $this->abortUnlessAdmin();

        DB::beginTransaction();

        try {
            // Delete cover image file if it exists
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

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
