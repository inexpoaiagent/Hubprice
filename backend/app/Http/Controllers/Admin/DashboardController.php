<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'total_users' => User::count(),
                'active_listings' => Listing::where('status', 'active')->count(),
                'pending_listings' => Listing::where('status', 'pending')->count(),
                'total_listings' => Listing::count(),
                'vehicle_listings' => Listing::where('type', 'vehicle')->count(),
                'property_listings' => Listing::where('type', 'property')->count(),
                'rental_listings' => Listing::where('type', 'rental')->count(),
                'sellers' => User::where('role', 'seller')->count(),
                'dealers' => User::where('role', 'dealer')->count(),
                'agencies' => User::where('role', 'agency')->count(),
                'buyers' => User::where('role', 'buyer')->count(),
            ],
            'recent_listings' => Listing::with(['user:id,name,email', 'listable'])->orderByDesc('created_at')->limit(10)->get(),
            'recent_users' => User::orderByDesc('created_at')->limit(10)->get(['id', 'name', 'email', 'role', 'created_at', 'is_active']),
        ]);
    }
}
