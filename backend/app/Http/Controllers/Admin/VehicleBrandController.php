<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleBrandController extends Controller
{
    public function index(): JsonResponse { return response()->json(VehicleBrand::orderBy('name')->get()); }
    public function store(Request $r): JsonResponse { return response()->json(VehicleBrand::create($r->all()), 201); }
    public function show(string $id): JsonResponse { return response()->json(VehicleBrand::findOrFail($id)); }
    public function update(Request $r, string $id): JsonResponse {
        $m = VehicleBrand::findOrFail($id); $m->update($r->all()); return response()->json($m);
    }
    public function destroy(string $id): JsonResponse {
        VehicleBrand::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted.']);
    }
}
