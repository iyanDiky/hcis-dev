<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SdmJenis;
use Illuminate\Support\Facades\Auth;

class SdmJenisController extends Controller
{
    /**
     * POST /api/sdm-jenis/list
     */
    public function list(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search', '');
        $page = $request->input('page', 1);

        $validSortColumns = ['jenis'];
        $sortByInput = $request->input('sort_by', 'jenis');
        $sortBy = in_array($sortByInput, $validSortColumns) ? $sortByInput : 'jenis';
        
        $sortDirInput = strtolower($request->input('sort_dir', 'asc'));
        $sortDir = in_array($sortDirInput, ['asc', 'desc']) ? $sortDirInput : 'asc';

        $query = SdmJenis::query();

        if (!empty($search)) {
            $query->where('jenis', 'ilike', '%' . $search . '%');
        }

        $query->orderByRaw('LOWER(' . $sortBy . ') ' . $sortDir);

        $data = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * POST /api/sdm-jenis/all
     * Endpoint untuk list dropdown tanpa paginasi
     */
    public function all(Request $request)
    {
        $data = SdmJenis::orderBy('jenis', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * POST /api/sdm-jenis/detail
     */
    public function detail(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $sdmJenis = SdmJenis::find($request->id);

        if (!$sdmJenis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $sdmJenis
        ]);
    }

    /**
     * POST /api/sdm-jenis/create
     */
    public function create(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string|max:255'
        ]);

        $user = $request->user();

        $sdmJenis = SdmJenis::create([
            'jenis' => $request->jenis,
            // AuditTrail trait will handle created_by/updated_by if configured properly,
            // but just to be safe and match MsProvinsiController behavior if not:
            'created_by' => $user ? $user->name : 'system',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $sdmJenis
        ]);
    }

    /**
     * POST /api/sdm-jenis/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid',
            'jenis' => 'required|string|max:255'
        ]);

        $sdmJenis = SdmJenis::find($request->id);

        if (!$sdmJenis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        $sdmJenis->update([
            'jenis' => $request->jenis,
            'updated_by' => $user ? $user->name : 'system',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate',
            'data' => $sdmJenis
        ]);
    }

    /**
     * POST /api/sdm-jenis/delete
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $sdmJenis = SdmJenis::find($request->id);

        if (!$sdmJenis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        // Update delete_by directly before soft deleting
        // If AuditTrail trait is present, it might handle this, but let's be explicit
        $sdmJenis->delete_by = $user ? $user->name : 'system';
        $sdmJenis->save();

        $sdmJenis->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
