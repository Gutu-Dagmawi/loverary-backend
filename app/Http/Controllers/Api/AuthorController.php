<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthorController extends Controller
{
    public function index(): JsonResponse
    {
        $authors = Author::all();

        return response()->json([
            'status' => true,
            'data' => $authors
        ], 200);
    }

    public function show(Author $author): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $author
        ], 200);
    }

    public function showMultipleAuthors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:authors,author_id'
        ]);

        try {
            $authors = Author::whereIn('author_id', $validated['ids'])->get();

            return response()->json([
                'status' => true,
                'data' => $authors
            ], 200);

        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $this->abortUnlessAdmin();
        try {
            $validateAuthor = Validator::make(
                $request->all(),
                [
                    'full_name' => 'required|string|max:255',
                    'gender' => 'required|in:male,female',
                ]
            );

            if ($validateAuthor->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateAuthor->errors()
                ], 422);
            }

            $author = Author::create([
                'full_name' => $request->full_name,
                'gender' => $request->gender
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Author created successfully',
                'data' => $author
            ], 201);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Author $author): JsonResponse
    {
        $this->abortUnlessAdmin();
        try {
            $validateAuthor = Validator::make(
                $request->all(),
                [
                    'full_name' => 'sometimes|required|string|max:255',
                    'gender' => 'sometimes|required|in:male,female',
                ]
            );

            if ($validateAuthor->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateAuthor->errors()
                ], 422);
            }

            $author->update($request->only(['full_name', 'gender']));

            return response()->json([
                'status' => true,
                'message' => 'Author updated successfully',
                'data' => $author->fresh()
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Author $author): JsonResponse
    {
        $this->abortUnlessAdmin();
        try {
            $author->delete();

            return response()->json([
                'status' => true,
                'message' => 'Author deleted successfully'
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
