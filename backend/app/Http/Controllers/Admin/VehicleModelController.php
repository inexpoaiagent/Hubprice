<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    public function index(): JsonResponse { return response()->json(VehicleModel::orderBy('name')->get()); }
    public function store(Request $r): JsonResponse { return response()->json(VehicleModel::create($r->all()), 201); }
    public function show(string $id): JsonResponse { return response()->json(VehicleModel::findOrFail($id)); }
    public function update(Request $r, string $id): JsonResponse {
        $m = VehicleModel::findOrFail($id); $m->update($r->all()); return response()->json($m);
    }
    public function destroy(string $id): JsonResponse {
        VehicleModel::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted.']);
    }
}
