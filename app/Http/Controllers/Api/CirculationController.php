<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Member;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Throwable;
use function Pest\Laravel\get;

class CirculationController extends Controller
{
    public function checkOutBook(Book $book): JsonResponse
    {
        $member = auth()->user();
        $availableCopy = $book->bookCopies()->where('is_available', true)->first();

        if (!$member instanceof Member) {
            return response()->json([
                'status' => false,
                'message' => 'Only members can check out books.',
            ], 403);
        }

        if ($availableCopy) {
            DB::beginTransaction();

            try {
                $checkOut = CheckOut::create([
                    'member_id' => $member->id,
                    'due_date' => Date::now()->addWeeks(2),
                    'book_copy_barcode' => $availableCopy->barcode,
                ]);

                $availableCopy->is_available = false;
                $availableCopy->save();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => "Successfully checked out {$book->title}",
                    'check_out' => $checkOut
                ]);
            } catch (Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'status' => false,
            'message' => "{$book->title} has no available copies"
        ]);
    }


    public function checkInBook(CheckOut $checkOut): JsonResponse
    {
        $bookCopy = $checkOut->bookCopy;
        $book = $bookCopy->book;
        $member = auth()->user();
        if (!$member instanceof Member) {
            return response()->json([
                'status' => false,
                'message' => 'Only members can check out books.',
            ], 403);
        }
        if ($checkOut->status === 'returned') {
            return response()->json([
                'status' => false,
                'message' => "Book copy [{$checkOut->book_copy_barcode}] of '{$book->title}' has already been returned. Nothing to check in.",
            ], 400);
        }

        try {
            DB::beginTransaction();

            $checkIn = CheckIn::create([
                'check_out_id' => $checkOut->check_out_id,
            ]);

            $checkOut->check_in_id = $checkIn->check_in_id;
            $checkOut->save();

            $bookCopy->is_available = true;
            $bookCopy->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Book copy successfully checked in.",
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to check in book: ' . $th->getMessage(),
            ], 500);
        }
    }
}
