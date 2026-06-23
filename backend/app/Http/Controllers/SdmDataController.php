<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SdmData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SdmDataController extends Controller
{
    /**
     * POST /api/sdm-data/list
     */
    public function list(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search', '');
        $page = $request->input('page', 1);

        $validSortColumns = ['nama', 'nik', 'email', 'nomor_telp', 'jk'];
        $sortByInput = $request->input('sort_by', 'nama');
        $sortBy = in_array($sortByInput, $validSortColumns) ? $sortByInput : 'nama';
        
        $sortDirInput = strtolower($request->input('sort_dir', 'asc'));
        $sortDir = in_array($sortDirInput, ['asc', 'desc']) ? $sortDirInput : 'asc';

        $query = SdmData::query()
            ->with(['kotaKabKtp', 'kotaKabDomisili']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ilike', '%' . $search . '%')
                  ->orWhere('nik', 'ilike', '%' . $search . '%')
                  ->orWhere('email', 'ilike', '%' . $search . '%')
                  ->orWhere('nomor_telp', 'ilike', '%' . $search . '%');
            });
        }

        $query->orderByRaw('LOWER(' . $sortBy . ') ' . $sortDir);

        $data = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * POST /api/sdm-data/detail
     */
    public function detail(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $sdmData = SdmData::with(['kotaKabKtp', 'kotaKabDomisili'])->find($request->id);

        if (!$sdmData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $sdmData
        ]);
    }

    /**
     * POST /api/sdm-data/create
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:sdm_data,email',
            'nik' => 'required|string|max:16|unique:sdm_data,nik',
            'nama' => 'required|string|max:255',
            'jk' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:255',
            'gol_darah' => 'nullable|in:A,B,AB,O',
            'status_pernikahan' => 'required|in:B,M,J,D,P',
            'foto' => 'nullable|image|max:2048',
            'spesimen_tanda_tangan' => 'nullable|image|max:2048',
            'spesimen_paraf' => 'nullable|image|max:2048',
            'npwp' => 'nullable|string|max:255',
            'nomor_telp' => 'required|string|max:15|unique:sdm_data,nomor_telp',
            'alamat_ktp' => 'required|string',
            'kota_kab_ktp' => 'nullable|string|uuid',
            'alamat_domisili' => 'required|string',
            'kota_kab_domisili' => 'nullable|string|uuid'
        ]);

        try {
            $user = $request->user();

            $fotoPath = $request->hasFile('foto') ? $request->file('foto')->store('uploads/sdm', 'public') : null;
            $ttdPath = $request->hasFile('spesimen_tanda_tangan') ? $request->file('spesimen_tanda_tangan')->store('uploads/sdm', 'public') : null;
            $parafPath = $request->hasFile('spesimen_paraf') ? $request->file('spesimen_paraf')->store('uploads/sdm', 'public') : null;

            $sdmData = SdmData::create([
                'email' => $request->email,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'jk' => $request->jk,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'gol_darah' => $request->gol_darah,
                'status_pernikahan' => $request->status_pernikahan,
                'foto' => $fotoPath ? '/storage/' . $fotoPath : null,
                'spesimen_tanda_tangan' => $ttdPath ? '/storage/' . $ttdPath : null,
                'spesimen_paraf' => $parafPath ? '/storage/' . $parafPath : null,
                'npwp' => $request->npwp,
                'nomor_telp' => $request->nomor_telp,
                'alamat_ktp' => $request->alamat_ktp,
                'kota_kab_ktp' => $request->kota_kab_ktp,
                'alamat_domisili' => $request->alamat_domisili,
                'kota_kab_domisili' => $request->kota_kab_domisili,
                'created_by' => $user ? $user->name : 'system',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data' => $sdmData
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating sdm_data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    /**
     * POST /api/sdm-data/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid',
            'email' => 'required|email|unique:sdm_data,email,' . $request->id,
            'nik' => 'required|string|max:16|unique:sdm_data,nik,' . $request->id,
            'nama' => 'required|string|max:255',
            'jk' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:255',
            'gol_darah' => 'nullable|in:A,B,AB,O',
            'status_pernikahan' => 'required|in:B,M,J,D,P',
            'foto' => 'nullable|image|max:2048',
            'spesimen_tanda_tangan' => 'nullable|image|max:2048',
            'spesimen_paraf' => 'nullable|image|max:2048',
            'npwp' => 'nullable|string|max:255',
            'nomor_telp' => 'required|string|max:15|unique:sdm_data,nomor_telp,' . $request->id,
            'alamat_ktp' => 'required|string',
            'kota_kab_ktp' => 'nullable|string|uuid',
            'alamat_domisili' => 'required|string',
            'kota_kab_domisili' => 'nullable|string|uuid'
        ]);

        $sdmData = SdmData::find($request->id);

        if (!$sdmData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        try {
            $user = $request->user();

            $fotoPath = $request->hasFile('foto') ? '/storage/' . $request->file('foto')->store('uploads/sdm', 'public') : $sdmData->foto;
            $ttdPath = $request->hasFile('spesimen_tanda_tangan') ? '/storage/' . $request->file('spesimen_tanda_tangan')->store('uploads/sdm', 'public') : $sdmData->spesimen_tanda_tangan;
            $parafPath = $request->hasFile('spesimen_paraf') ? '/storage/' . $request->file('spesimen_paraf')->store('uploads/sdm', 'public') : $sdmData->spesimen_paraf;

            $sdmData->update([
                'email' => $request->email,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'jk' => $request->jk,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'gol_darah' => $request->gol_darah,
                'status_pernikahan' => $request->status_pernikahan,
                'foto' => $fotoPath,
                'spesimen_tanda_tangan' => $ttdPath,
                'spesimen_paraf' => $parafPath,
                'npwp' => $request->npwp,
                'nomor_telp' => $request->nomor_telp,
                'alamat_ktp' => $request->alamat_ktp,
                'kota_kab_ktp' => $request->kota_kab_ktp,
                'alamat_domisili' => $request->alamat_domisili,
                'kota_kab_domisili' => $request->kota_kab_domisili,
                'updated_by' => $user ? $user->name : 'system',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diupdate',
                'data' => $sdmData
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating sdm_data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate data'
            ], 500);
        }
    }

    /**
     * POST /api/sdm-data/delete
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|string|uuid'
        ]);

        $sdmData = SdmData::find($request->id);

        if (!$sdmData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        try {
            $user = $request->user();

            // Update delete_by directly before soft deleting
            $sdmData->delete_by = $user ? $user->name : 'system';
            $sdmData->save();

            $sdmData->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting sdm_data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }
}
