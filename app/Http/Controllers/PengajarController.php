<?php
namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presence;
use App\Models\Contract;
use Illuminate\Support\Facades\Storage;

class PengajarController extends Controller
{
    public function showTeacherList()
    {
        $teachers = User::where('role', 'pengajar')->get();
        return view('admin.data-pengajar-admin', compact('teachers'));
    }

    public function showTeacherDetail($id)
    {
        $teacher = User::findOrFail($id);
        $presences = Presence::where('user_id', $id)->orderBy('date', 'desc')->paginate(12);
        $contracts = $teacher->role === 'pengajar' ? Contract::where('user_id', $id)->orderBy('start_date', 'desc')->paginate(2) : collect([]);
        $isPengajar = $teacher->role === 'pengajar';

        return view('admin.detail-pengajar-admin', compact('teacher', 'presences', 'contracts', 'isPengajar'));
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true, 'message' => 'Password telah direset.']);
    }

    public function storeContract(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'pengajar') {
            return response()->json(['success' => false, 'message' => 'Kontrak hanya berlaku untuk pengajar.'], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|string|in:active,expired,terminated',
            'document' => 'nullable|string',
        ]);

        Contract::create([
            'user_id' => $id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'document' => $request->document,
        ]);

        return response()->json(['success' => true, 'message' => 'Kontrak ditambahkan.']);
    }

    public function updateContract(Request $request, $id, $contract_id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'pengajar') {
            return response()->json(['success' => false, 'message' => 'Kontrak hanya berlaku untuk pengajar.'], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|string|in:active,expired,terminated',
            'document' => 'nullable|string',
        ]);

        $contract = Contract::findOrFail($contract_id);
        $contract->update($request->only(['start_date', 'end_date', 'status', 'document']));

        return response()->json(['success' => true, 'message' => 'Kontrak diperbarui.']);
    }

    public function deleteContract($id, $contract_id)
    {
        $user = User::findOrFail($id);
        if ($user->role !== 'pengajar') {
            return response()->json(['success' => false, 'message' => 'Kontrak hanya berlaku untuk pengajar.'], 403);
        }

        $contract = Contract::findOrFail($contract_id);
        $contract->delete();

        return response()->json(['success' => true, 'message' => 'Kontrak dihapus.']);
    }

    public function showRegistrationRequests()
    {
        $users = User::where('accepted', false)
                     ->where('role', 'pengajar')
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('admin.registration-request-admin', compact('users'));
    }

    public function acceptRegistration($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'accepted' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil diterima.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function rejectRegistration($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil ditolak.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
