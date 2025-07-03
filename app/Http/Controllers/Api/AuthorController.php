<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthorController extends Controller
{
    public function store(Request $request): JsonResponse
    {

        try {
            $validateAuthor = Validator::make(
                $request->all(),
                [
                    'full_name' => 'required',
                    'gender' => 'required|in:male,female',
                ]
            );
            if ($validateAuthor->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateAuthor->errors()
                ], 401);
            }
            $author = Author::create(
                [
                    'full_name' => $request->full_name,
                    'gender' => $request->gender
                ]
            );
            return response()->json([
                'status' => true,
                'message' => 'Author Created Successfully',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
