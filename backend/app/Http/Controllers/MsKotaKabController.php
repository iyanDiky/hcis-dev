<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MsKotaKab;
use Illuminate\Support\Facades\Auth;

class MsKotaKabController extends Controller
{
    /**
     * POST /api/kota/list
     */
    public function list(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search', '');
        $page = $request->input('page', 1);

        $validSortColumns = ['kota_kabupaten', 'provinsi'];
        $sortByInput = $request->input('sort_by', 'kota_kabupaten');
        $sortBy = in_array($sortByInput, $validSortColumns) ? $sortByInput : 'kota_kabupaten';
        
        $sortDirInput = strtolower($request->input('sort_dir', 'asc'));
        $sortDir = in_array($sortDirInput, ['asc', 'desc']) ? $sortDirInput : 'asc';

        $query = MsKotaKab::select('ms_kota_kab.*')
            ->leftJoin('ms_provinsi', 'ms_kota_kab.provinsi', '=', 'ms_provinsi.id')
            ->with('provinsiRel');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('ms_kota_kab.kota_kabupaten', 'ilike', '%' . $search . '%')
                  ->orWhere('ms_provinsi.provinsi', 'ilike', '%' . $search . '%');
            });
        }

        if ($sortBy === 'provinsi') {
            $query->orderByRaw('LOWER(ms_provinsi.provinsi) ' . $sortDir);
        } else {
            $query->orderByRaw('LOWER(ms_kota_kab.' . $sortBy . ') ' . $sortDir);
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * POST /api/kota/detail
     */
    public function detail(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $kota = MsKotaKab::with('provinsiRel')->find($request->id);

        if (!$kota) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kota
        ]);
    }

    /**
     * POST /api/kota/create
     */
    public function create(Request $request)
    {
        $request->validate([
            'kota_kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|uuid'
        ]);

        $user = $request->user();

        $kota = MsKotaKab::create([
            'kota_kabupaten' => $request->kota_kabupaten,
            'provinsi' => $request->provinsi,
            'created_by' => $user ? $user->name : 'system',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $kota
        ]);
    }

    /**
     * POST /api/kota/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid',
            'kota_kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|uuid'
        ]);

        $kota = MsKotaKab::find($request->id);

        if (!$kota) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        $kota->update([
            'kota_kabupaten' => $request->kota_kabupaten,
            'provinsi' => $request->provinsi,
            'updated_by' => $user ? $user->name : 'system',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate',
            'data' => $kota
        ]);
    }

    /**
     * POST /api/kota/delete
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $kota = MsKotaKab::find($request->id);

        if (!$kota) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = $request->user();

        // Update delete_by directly before soft deleting
        $kota->delete_by = $user ? $user->name : 'system';
        $kota->save();

        $kota->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * GET/POST /api/kota/all
     */
    public function all(Request $request)
    {
        $data = MsKotaKab::select('id', 'kota_kabupaten', 'provinsi')
            ->with('provinsiRel:id,provinsi')
            ->orderBy('kota_kabupaten', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
