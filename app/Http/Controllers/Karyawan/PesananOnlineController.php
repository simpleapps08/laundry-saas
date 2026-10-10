<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PesananOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * F2 — Kelola order online yang masuk (sisi kasir).
 *
 * Kasir melihat daftar pesanan "Menunggu", lalu memprosesnya menjadi
 * transaksi asli lewat form add-order yang sudah ada.
 */
class PesananOnlineController extends Controller
{
    /** Daftar order online masuk (scoped ke cabang kasir via MilikCabang-like filter). */
    public function index(Request $request)
    {
        $user = Auth::user();
        $cabangIds = $user->cabangIds();

        $status = $request->query('status', 'Menunggu');

        $query = PesananOnline::with('cabang')->orderBy('id', 'DESC');
        if (! empty($cabangIds)) {
            $query->whereIn('cabang_id', $cabangIds);
        }
        if ($status !== 'semua') {
            $query->where('status_online', $status);
        }

        $pesanan = $query->get();
        $jumlahMenunggu = PesananOnline::whereIn('cabang_id', $cabangIds ?: [0])
            ->where('status_online', 'Menunggu')->count();

        return view('karyawan.pesanan-online.index', compact('pesanan', 'status', 'jumlahMenunggu'));
    }

    /**
     * Tandai pesanan sudah diproses (setelah kasir simpan transaksi asli).
     * Dipanggil dari form add-order saat menyimpan (opsional, via kode).
     */
    public function tandaiDiproses(Request $request)
    {
        $data = $request->validate([
            'kode_pesanan'  => 'required|string',
            'transaksi_id'  => 'required|integer',
        ]);

        $user = Auth::user();
        $pesanan = PesananOnline::where('kode_pesanan', $data['kode_pesanan'])->firstOrFail();

        if (! $user->bolehAksesCabang($pesanan->cabang_id)) {
            abort(403, 'Pesanan ini bukan milik cabang Anda.');
        }

        $pesanan->update([
            'status_online' => 'Diproses',
            'transaksi_id'  => $data['transaksi_id'],
            'diproses_at'   => now(),
        ]);

        return response()->json(['ok' => true, 'status' => $pesanan->status_online]);
    }

    /** Batalkan pesanan online (mis. pelanggan tidak jadi). */
    public function batalkan($id)
    {
        $user = Auth::user();
        $pesanan = PesananOnline::findOrFail($id);

        if (! $user->bolehAksesCabang($pesanan->cabang_id)) {
            abort(403, 'Pesanan ini bukan milik cabang Anda.');
        }

        $pesanan->update(['status_online' => 'Dibatalkan']);

        return back()->with('success', 'Pesanan online dibatalkan.');
    }
}
