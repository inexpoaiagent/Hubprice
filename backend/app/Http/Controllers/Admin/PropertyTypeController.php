<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index(): JsonResponse { return response()->json(PropertyType::orderBy('name')->get()); }
    public function store(Request $r): JsonResponse { return response()->json(PropertyType::create($r->all()), 201); }
    public function show(string $id): JsonResponse { return response()->json(PropertyType::findOrFail($id)); }
    public function update(Request $r, string $id): JsonResponse {
        $m = PropertyType::findOrFail($id); $m->update($r->all()); return response()->json($m);
    }
    public function destroy(string $id): JsonResponse {
        PropertyType::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted.']);
    }
}
