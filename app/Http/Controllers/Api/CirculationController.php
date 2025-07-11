<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Throwable;
use function Pest\Laravel\get;

class CirculationController extends Controller
{
    public function getAllLoans(): JsonResponse
    {
        $member = auth()->user();
        if (!($member->isAdmin())) {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can view all checkouts.',
                'current_user' => $member,
            ], 403);
        }

        $checkOuts = CheckOut::all();

        return response()->json([
            'status' => true,
            'check_outs' => $checkOuts
        ]);
    }

    public function getAllOverdue(): JsonResponse
    {
        $member = auth()->user();
        if (!$member->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can view all overdue books.',
                'current_user' => $member,
            ], 403);
        }

        $overdueCheckOuts = CheckOut::where('due_date', '<', Date::now())
            ->whereNull('check_in_id')
            ->get();

        if ($overdueCheckOuts->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No overdue checkouts found.',
                'total' => 0
            ], 200);
        }

        return response()->json([
            'status' => true,
            'overdue_check_outs' => $overdueCheckOuts,
            'total' => $overdueCheckOuts->count()
        ]);
    }


    public function getAllReturns(): JsonResponse
    {
        $member = auth()->user();
        if (!$member->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can view all returns.',
                'current_user' => $member,
            ], 403);
        }

        $returns = CheckIn::all();

        if ($returns->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No returns found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'returns' => $returns
        ]);
    }


    public function getActiveLoans(): JsonResponse
    {
        $member = auth()->user();
        if (!$member->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can view all active loans.',
            ], 403);
        }

        $activeLoans = CheckOut::whereNull('check_in_id')
            ->get();

        if ($activeLoans->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active loans found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'active_loans' => $activeLoans,
            'total' => $activeLoans->count()
        ]);
    }
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

        // Check if the member has more than 3 active loans and prevent further checkouts
        $activeLoansCount = CheckOut::where('member_id', $member->id)
            ->whereNull('check_in_id')
            ->count();
            
        if ($activeLoansCount >= 3) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot check out more than 3 books at a time.',
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
