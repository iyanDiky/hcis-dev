<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MsProvinsi;
use Illuminate\Support\Facades\Auth;

class MsProvinsiController extends Controller
{
    /**
     * POST /api/provinsi/list
     */
    public function list(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search', '');
        $page = $request->input('page', 1);

        $validSortColumns = ['provinsi'];
        $sortByInput = $request->input('sort_by', 'provinsi');
        $sortBy = in_array($sortByInput, $validSortColumns) ? $sortByInput : 'provinsi';
        
        $sortDirInput = strtolower($request->input('sort_dir', 'asc'));
        $sortDir = in_array($sortDirInput, ['asc', 'desc']) ? $sortDirInput : 'asc';

        $query = MsProvinsi::query();

        if (!empty($search)) {
            $query->where('provinsi', 'ilike', '%' . $search . '%');
        }

        $query->orderByRaw('LOWER(' . $sortBy . ') ' . $sortDir);

        $data = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * POST /api/provinsi/detail
     */
    public function detail(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $provinsi = MsProvinsi::find($request->id);

        if (!$provinsi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $provinsi
        ]);
    }

    /**
     * POST /api/provinsi/create
     */
    public function create(Request $request)
    {
        $request->validate([
            'provinsi' => 'required|string|max:255'
        ]);

        $user = $request->user();

        $provinsi = MsProvinsi::create([
            'provinsi' => $request->provinsi,
            'created_by' => $user ? $user->name : 'system',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $provinsi
        ]);
    }

    /**
     * POST /api/provinsi/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid',
            'provinsi' => 'required|string|max:255'
        ]);

        $provinsi = MsProvinsi::find($request->id);

        if (!$provinsi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        $provinsi->update([
            'provinsi' => $request->provinsi,
            'updated_by' => $user ? $user->name : 'system',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate',
            'data' => $provinsi
        ]);
    }

    /**
     * POST /api/provinsi/delete
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $provinsi = MsProvinsi::find($request->id);

        if (!$provinsi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        // Update delete_by directly before soft deleting
        $provinsi->delete_by = $user ? $user->name : 'system';
        $provinsi->save();

        $provinsi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
