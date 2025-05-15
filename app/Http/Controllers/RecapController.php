<?php

namespace App\Http\Controllers;

use App\Models\Recap;
use App\Models\User;
use App\Models\Presence;
use Illuminate\Http\Request;
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
        // Log input untuk debugging
        \Log::info('Tanggal input: ' . print_r($request->input('tanggal'), true));

        // Ambil input tanggal dan konversi ke array jika berupa string
        $tanggalInput = $request->input('tanggal');
        if (is_string($tanggalInput)) {
            $tanggal = array_map('trim', explode(',', $tanggalInput));
            // Validasi format Y-m-d untuk setiap tanggal
            foreach ($tanggal as $date) {
                if (!Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta')) {
                    return redirect()->back()->with('error', 'Tanggal tidak valid: ' . $date)->withInput();
                }
            }
        } else {
            $tanggal = is_array($tanggalInput) ? $tanggalInput : [];
        }

        // Gabungkan input lain dengan tanggal yang sudah dikonversi
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
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal menyimpan rekap. Periksa input Anda.');
        }

        $periode = $this->getPeriodeFromTanggal($tanggal);

        Recap::create([
            'periode' => $periode,
            'batas_keterlambatan' => $request->batas_keterlambatan,
            'mukafaah' => $request->mukafaah,
            'bonus' => $request->bonus,
            'dates' => json_encode($tanggal),
        ]);

        return redirect()->route('recaps.index')->with('success', 'Rekap presensi berhasil disimpan.');
    }

    public function show($id)
    {
        $recap = Recap::findOrFail($id);
        $pengajars = User::where('role', 'pengajar')->where('accepted', '1')->get();
        $dates = json_decode($recap->dates, true);
        $presences = Presence::whereIn('date', $dates)
            ->whereIn('user_id', $pengajars->pluck('id'))
            ->get()
            ->groupBy('user_id');
        \Log::info('Recap ID: ' . $id . ', Batas Keterlambatan: ' . $recap->batas_keterlambatan . ', Mukafaah: ' . $recap->mukafaah . ', Bonus: ' . $recap->bonus);
        \Log::info('Dates: ' . json_encode($dates));
        \Log::info('Presences: ' . json_encode($presences->toArray()));
        return view('admin.details-recap-admin', compact('recap', 'pengajars', 'dates', 'presences'));
    }

    public function edit($id)
    {
        $recap = Recap::findOrFail($id);
        return view('admin.data-recap-admin', ['recaps' => Recap::all(), 'editRecap' => $recap]);
    }

    public function update(Request $request, $id)
    {
        $recap = Recap::findOrFail($id);

        // Log input untuk debugging
        \Log::info('Tanggal input: ' . print_r($request->input('tanggal'), true));

        // Ambil input tanggal dan konversi ke array jika berupa string
        $tanggalInput = $request->input('tanggal');
        if (is_string($tanggalInput)) {
            $tanggal = array_map('trim', explode(',', $tanggalInput));
            // Validasi format Y-m-d untuk setiap tanggal
            foreach ($tanggal as $date) {
                if (!Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta')) {
                    return redirect()->back()->with('error', 'Tanggal tidak valid: ' . $date)->withInput();
                }
            }
        } else {
            $tanggal = is_array($tanggalInput) ? $tanggalInput : [];
        }

        // Gabungkan input lain dengan tanggal yang sudah dikonversi
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
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Gagal memperbarui rekap. Periksa input Anda.');
        }

        $periode = $this->getPeriodeFromTanggal($tanggal);

        $recap->update([
            'periode' => $periode,
            'batas_keterlambatan' => $request->batas_keterlambatan,
            'mukafaah' => $request->mukafaah,
            'bonus' => $request->bonus,
            'dates' => json_encode($tanggal),
        ]);

        return redirect()->route('recaps.index')->with('success', 'Rekap presensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $recap = Recap::findOrFail($id);
        $recap->delete();
        return redirect()->route('recaps.index')->with('success', 'Rekap presensi berhasil dihapus.');
    }

    public function filter(Request $request)
    {
        $periode = $request->input('periode');
        $query = Recap::query();

        if ($periode) {
            $query->where('periode', $periode);
        }

        $recaps = $query->get();
        return view('admin.data-recap-admin', compact('recaps'));
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
