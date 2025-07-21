<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number',
            'account_holder' => 'required|string|max:255',
        ]);

        $bankAccount = BankAccount::create([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekening berhasil ditambahkan.',
            'data' => $bankAccount
        ], 201);
    }

    public function edit($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        return response()->json(['success' => true, 'data' => $bankAccount], 200);
    }

    public function update(Request $request, $id)
    {
        $bankAccount = BankAccount::findOrFail($id);

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $id,
            'account_holder' => 'required|string|max:255',
        ]);

        $bankAccount->update([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekening berhasil diperbarui.',
            'data' => $bankAccount
        ], 200);
    }

    public function destroy($id)
    {
        $bankAccount = BankAccount::findOrFail($id);

        // Cek apakah rekening digunakan di pengeluaran
        if ($bankAccount->pengeluaranBulanan()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus rekening yang masih digunakan di pengeluaran.'
            ], 422);
        }

        $bankAccount->delete();
        return response()->json([
            'success' => true,
            'message' => 'Rekening berhasil dihapus.'
        ], 200);
    }
}
