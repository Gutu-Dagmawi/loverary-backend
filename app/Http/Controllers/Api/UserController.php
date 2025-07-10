<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends Controller
{
    /**
     * Get all members of the library.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllMembers(): JsonResponse
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Only admins can view all members.',
            ], 403);
        }

        $members = Member::all();
        if($members->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No members found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'members' => $members
        ]);
    }

    public function getMember($id): JsonResponse
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status' => false,
                'message' => 'Member not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'member' => $member
        ]);
    }
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function returns(): JsonResponse
    {
        $member = Auth::user();

        if (!$member instanceof Member) {
            return response()->json([
                'status' => false,
                'message' => 'Only members can have loans to list.',
            ], 403);
        }

        $pageSize = intval(request()->get('per_page', 10));
        $pageSize = max(1, min($pageSize, 100));

        $paginated = $member->check_outs()
            ->whereNotNull('check_in_id')
            ->paginate($pageSize);

        return response()->json([
            'status' => true,
            'returns' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'has_more' => $paginated->hasMorePages(),
            ]
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loans(): JsonResponse
    {
        $member = Auth::user();

        if (!$member instanceof Member) {
            return response()->json([
                'status' => false,
                'message' => 'Only members can have loans to list.',
            ], 403);
        }

        $pageSize = intval(request()->get('per_page', 10));
        $pageSize = max(1, min($pageSize, 100));

        $paginated = $member->check_outs()
            ->whereNull('check_in_id')
            ->paginate($pageSize);

        return response()->json([
            'status' => true,
            'loans' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'has_more' => $paginated->hasMorePages(),
            ]
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function overdue(): JsonResponse
    {
        $member = Auth::user();

        if (!$member instanceof Member) {
            return response()->json([
                'status' => false,
                'message' => 'Only members can have overdue loans to list.',
            ], 403);
        }

        $pageSize = intval(request()->get('per_page', 10));
        $pageSize = max(1, min($pageSize, 100));

        $paginated = $member->check_outs()
            ->where('due_date', '<', Date::now())
            ->whereNull('check_in_id')
            ->paginate($pageSize);

        return response()->json([
            'status' => true,
            'overdue_loans' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'has_more' => $paginated->hasMorePages(),
            ]
        ]);
    }
}
