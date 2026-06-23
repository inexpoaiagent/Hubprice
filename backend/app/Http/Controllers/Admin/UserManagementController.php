<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = User::withTrashed()->orderByDesc('created_at');
        if ($request->role) $q->where('role', $request->role);
        if ($request->search) $q->where(function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%');
        });
        return response()->json($q->paginate(20));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(User::withTrashed()->with(['profile', 'kyc', 'listings', 'activeSubscription.plan'])->findOrFail($id));
    }

    public function toggleActive(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        return response()->json(['is_active' => $user->is_active]);
    }

    public function updateRole(Request $request, string $id): JsonResponse
    {
        $request->validate(['role' => 'required|in:buyer,seller,dealer,agency,investor,premium,moderator,support,finance_manager,ai_analyst,admin']);
        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);
        $user->syncRoles([$request->role]);
        return response()->json(['message' => 'Role updated.', 'user' => $user]);
    }

    public function approveKyc(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->kyc?->update(['status' => 'approved', 'verified_at' => now()]);
        $user->update(['kyc_verified' => true]);
        return response()->json(['message' => 'KYC approved.']);
    }

    public function destroy(string $id): JsonResponse
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted.']);
    }
}
