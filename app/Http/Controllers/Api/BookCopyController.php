<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookCopy;

class BookCopyController extends Controller
{
    public function getByBarcode($barcode)
    {
        $bookCopy = BookCopy::where('barcode', $barcode)->first();
        if (!$bookCopy) {
            return response()->json(['message' => 'Book copy not found'], 404);
        }

        $book = $bookCopy?->book;

        return response()->json([
            'message' => 'Book copy retrieved successfully',
            'barcode' => $barcode,
            'book' => $book,
        ]);
    }
}
