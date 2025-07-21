<?php

namespace App\Http\Controllers;

use App\Models\Recap;
use App\Models\User;
use App\Models\Presence;
use App\Models\AdditionalMukafaah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RecapController extends Controller
{
    public function index()
    {
        $recaps = Recap::all();
        return view('admin.data-recap-admin', compact('recaps'));
    }

    public function store(Request $request)
    {
        \Log::info('Tanggal input: ' . print_r($request->input('tanggal'), true));

        $tanggalInput = $request->input('tanggal');
        if (is_string($tanggalInput)) {
            $tanggal = array_map('trim', explode(',', $tanggalInput));
            foreach ($tanggal as $date) {
                if (!Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tanggal tidak valid: ' . $date
                    ], 422);
                }
            }
        } else {
            $tanggal = is_array($tanggalInput) ? $tanggalInput : [];
        }

        $data = $request->all();
        $data['tanggal'] = $tanggal;

        $validator = Validator::make($data, [
            'batas_keterlambatan' => 'required|date_format:H:i',
            'mukafaah' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'tanggal' => 'required|array|min:1',
            'tanggal.*' => 'date_format:Y-m-d',
        ], [
            'batas_keterlambatan.required' => 'Batas keterlambatan wajib diisi.',
            'batas_keterlambatan.date_format' => 'Format batas keterlambatan harus HH:mm.',
            'mukafaah.required' => 'Mukafaah wajib diisi.',
            'mukafaah.numeric' => 'Mukafaah harus berupa angka.',
            'bonus.required' => 'Bonus wajib diisi.',
            'bonus.numeric' => 'Bonus harus berupa angka.',
            'tanggal.required' => 'Tanggal presensi wajib dipilih.',
            'tanggal.array' => 'Tanggal presensi harus berupa array.',
            'tanggal.min' => 'Pilih minimal satu tanggal presensi.',
            'tanggal.*.date_format' => 'Format tanggal presensi harus YYYY-MM-DD.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $periode = $this->getPeriodeFromTanggal($tanggal);

        Recap::create([
            'periode' => $periode,
            'batas_keterlambatan' => $request->batas_keterlambatan,
            'mukafaah' => $request->mukafaah,
            'bonus' => $request->bonus,
            'dates' => json_encode($tanggal),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekap presensi berhasil disimpan.'
        ]);
    }

    public function show($id)
    {
        $recap = Recap::with('additionalMukafaahs')->findOrFail($id);
        $pengajars = User::where('role', 'pengajar')->where('accepted', '1')->get();
        $dates = json_decode($recap->dates, true);
        $presences = Presence::whereIn('date', $dates)
            ->whereIn('user_id', $pengajars->pluck('id'))
            ->get()
            ->groupBy('user_id');

        \Log::info('Recap ID: ' . $id . ', Batas Keterlambatan: ' . $recap->batas_keterlambatan . ', Mukafaah: ' . $recap->mukafaah . ', Bonus: ' . $recap->bonus);
        \Log::info('Dates: ' . json_encode($dates));
        \Log::info('Presences: ' . json_encode($presences->toArray()));
        \Log::info('Additional Mukafaahs: ' . json_encode($recap->additionalMukafaahs->toArray()));

        return view('admin.details-recap-admin', compact('recap', 'pengajars', 'dates', 'presences'));
    }

    public function edit($id)
    {
        $recap = Recap::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $recap
        ]);
    }

    public function update(Request $request, $id)
    {
        $recap = Recap::findOrFail($id);

        \Log::info('Tanggal input: ' . print_r($request->input('tanggal'), true));

        $tanggalInput = $request->input('tanggal');
        if (is_string($tanggalInput)) {
            $tanggal = array_map('trim', explode(',', $tanggalInput));
            foreach ($tanggal as $date) {
                if (!Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tanggal tidak valid: ' . $date
                    ], 422);
                }
            }
        } else {
            $tanggal = is_array($tanggalInput) ? $tanggalInput : [];
        }

        $data = $request->all();
        $data['tanggal'] = $tanggal;

        $validator = Validator::make($data, [
            'batas_keterlambatan' => 'required|date_format:H:i',
            'mukafaah' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'tanggal' => 'required|array|min:1',
            'tanggal.*' => 'date_format:Y-m-d',
        ], [
            'batas_keterlambatan.required' => 'Batas keterlambatan wajib diisi.',
            'batas_keterlambatan.date_format' => 'Format batas keterlambatan harus HH:mm.',
            'mukafaah.required' => 'Mukafaah wajib diisi.',
            'mukafaah.numeric' => 'Mukafaah harus berupa angka.',
            'bonus.required' => 'Bonus wajib diisi.',
            'bonus.numeric' => 'Bonus harus berupa angka.',
            'tanggal.required' => 'Tanggal presensi wajib dipilih.',
            'tanggal.array' => 'Tanggal presensi harus berupa array.',
            'tanggal.min' => 'Pilih minimal satu tanggal presensi.',
            'tanggal.*.date_format' => 'Format tanggal presensi harus YYYY-MM-DD.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $periode = $this->getPeriodeFromTanggal($tanggal);

        $recap->update([
            'periode' => $periode,
            'batas_keterlambatan' => $request->batas_keterlambatan,
            'mukafaah' => $request->mukafaah,
            'bonus' => $request->bonus,
            'dates' => json_encode($tanggal),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekap presensi berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $recap = Recap::findOrFail($id);
        $recap->delete();
        return response()->json([
            'success' => true,
            'message' => 'Rekap presensi berhasil dihapus.'
        ]);
    }

    public function filter(Request $request)
    {
        $periode = $request->input('periode');
        $query = Recap::query();

        if ($periode) {
            $query->where('periode', $periode);
        }

        $recaps = $query->get();
        return response()->json([
            'success' => true,
            'data' => $recaps
        ]);
    }

    public function storeAdditionalMukafaah(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Pengajar wajib dipilih.',
            'user_id.exists' => 'Pengajar tidak valid.',
            'amount.required' => 'Nominal mukafaah tambahan wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'description.string' => 'Keterangan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $recap = Recap::findOrFail($id);

        AdditionalMukafaah::create([
            'recap_id' => $recap->id,
            'user_id' => $request->user_id,
            'additional_mukafaah' => $request->amount,
            'description' => $request->description ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mukafaah tambahan berhasil disimpan.'
        ]);
    }

    public function editAdditionalMukafaah($id, $mukafaahId)
    {
        try {
            // Pastikan recap exists
            $recap = Recap::findOrFail($id);

            // Find mukafaah dengan kondisi yang benar
            $mukafaah = AdditionalMukafaah::where('recap_id', $id)
                ->where('id', $mukafaahId)
                ->first();

            if (!$mukafaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mukafaah tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $mukafaah
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in editAdditionalMukafaah: ' . $e->getMessage(), [
                'recap_id' => $id,
                'mukafaah_id' => $mukafaahId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAdditionalMukafaah(Request $request, $id, $mukafaahId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Pengajar wajib dipilih.',
            'user_id.exists' => 'Pengajar tidak valid.',
            'amount.required' => 'Nominal mukafaah tambahan wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'description.string' => 'Keterangan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Pastikan recap exists
            $recap = Recap::findOrFail($id);

            // Find dan update mukafaah
            $mukafaah = AdditionalMukafaah::where('recap_id', $id)
                ->where('id', $mukafaahId)
                ->first();

            if (!$mukafaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mukafaah tidak ditemukan.'
                ], 404);
            }

            $mukafaah->update([
                'user_id' => $request->user_id,
                'additional_mukafaah' => $request->amount,
                'description' => $request->description ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mukafaah tambahan berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in updateAdditionalMukafaah: ' . $e->getMessage(), [
                'recap_id' => $id,
                'mukafaah_id' => $mukafaahId,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyAdditionalMukafaah($id, $mukafaahId)
    {
        $mukafaah = AdditionalMukafaah::where('recap_id', $id)->findOrFail($mukafaahId);
        $mukafaah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mukafaah tambahan berhasil dihapus.'
        ]);
    }

    private function getPeriodeFromTanggal($tanggal)
    {
        if (empty($tanggal) || !is_array($tanggal) || !isset($tanggal[0])) {
            \Log::warning('Invalid tanggal input in getPeriodeFromTanggal', ['tanggal' => $tanggal]);
            return '';
        }
        try {
            $firstDate = Carbon::createFromFormat('Y-m-d', $tanggal[0], 'Asia/Jakarta');
            return $firstDate->format('Y-m');
        } catch (\Exception $e) {
            \Log::error('Error parsing date in getPeriodeFromTanggal: ' . $e->getMessage(), ['tanggal' => $tanggal[0]]);
            return '';
        }
    }
}
