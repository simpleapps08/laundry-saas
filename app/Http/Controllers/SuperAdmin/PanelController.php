<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Langganan;
use App\Models\Paket;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\transaksi;
use App\Services\FiturPaket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Panel Super-Admin — mengelola seluruh cabang & langganan.
 *
 * PENTING: semua query di sini memakai tanpaScopeCabang() /
 * withoutGlobalScope karena super-admin harus melihat lintas cabang.
 */
class PanelController extends Controller
{
    // ── Dashboard lintas cabang ─────────────────────────────────────────
    public function index()
    {
        $totalCabang = Cabang::count();
        $cabangAktif = Cabang::where('status', 'aktif')->count();

        $totalUser = User::withoutGlobalScope('cabang')->count();

        $totalPendapatan = transaksi::tanpaScopeCabang()
            ->where('status_payment', 'Success')
            ->sum('harga_akhir_numeric');

        // Pendapatan bulan ini
        $pendapatanBulan = transaksi::tanpaScopeCabang()
            ->where('status_payment', 'Success')
            ->whereYear('tanggal_masuk', date('Y'))
            ->whereMonth('tanggal_masuk', date('m'))
            ->sum('harga_akhir_numeric');

        // Langganan perlu perhatian
        $akanBerakhir = Langganan::with(['cabang', 'paket'])
            ->whereIn('status', ['trial', 'aktif'])
            ->whereNotNull('berakhir')
            ->whereBetween('berakhir', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('berakhir')
            ->get();

        $jatuhTempo = Langganan::with(['cabang', 'paket'])
            ->where('status', 'jatuh_tempo')
            ->orderBy('berakhir')
            ->get();

        // Tagihan belum lunas
        $tagihanTertunggak = Tagihan::with(['cabang'])
            ->belumBayar()
            ->orderBy('jatuh_tempo')
            ->limit(20)
            ->get();

        $nilaiTertunggak = Tagihan::belumBayar()->sum('jumlah');

        // Pendapatan langganan (MRR sederhana dari langganan aktif bulanan)
        $mrr = Langganan::where('status', 'aktif')
            ->where('siklus', 'bulanan')
            ->get()
            ->sum(fn ($l) => $l->hargaEfektif());

        return view('superadmin.index', compact(
            'totalCabang', 'cabangAktif', 'totalUser',
            'totalPendapatan', 'pendapatanBulan',
            'akanBerakhir', 'jatuhTempo', 'tagihanTertunggak', 'nilaiTertunggak',
            'mrr'
        ));
    }

    // ── Daftar cabang ───────────────────────────────────────────────────
    public function cabang()
    {
        // Catatan: langgananAktif() mengembalikan Model (bukan relasi),
        // jadi tidak bisa di-eager-load lewat with(). Muat relasi
        // `langganan.paket` lalu pilih yang berlaku saat render.
        $cabang = Cabang::withCount(['users as jumlah_user'])
          ->with(['langganan.paket'])
          ->orderBy('nama')
          ->get();

        // hitung transaksi & pendapatan per cabang (hindari N+1)
        $stat = transaksi::tanpaScopeCabang()
            ->select('cabang_id',
                DB::raw('count(*) as jml'),
                DB::raw('COALESCE(SUM(harga_akhir_numeric),0) as total'))
            ->groupBy('cabang_id')
            ->get()
            ->keyBy('cabang_id');

        return view('superadmin.cabang', compact('cabang', 'stat'));
    }

    // ── Daftar langganan ────────────────────────────────────────────────
    public function langganan()
    {
        $langganan = Langganan::with(['cabang', 'paket'])
            ->orderByDesc('id')
            ->get();

        $paket = Paket::aktif()->get();

        return view('superadmin.langganan', compact('langganan', 'paket'));
    }

    // ── Simpan/perbarui langganan cabang ────────────────────────────────
    public function simpanLangganan(Request $request)
    {
        $data = $request->validate([
            'cabang_id' => 'required|exists:cabang,id',
            'paket_id' => 'required|exists:paket,id',
            'siklus' => 'required|in:bulanan,tahunan',
            'status' => 'required|in:trial,aktif,jatuh_tempo,berhenti',
            'mulai' => 'required|date',
            'berakhir' => 'required|date|after:mulai',
            'catatan' => 'nullable|string',
        ]);

        Langganan::updateOrCreate(
            ['cabang_id' => $data['cabang_id']],
            $data
        );

        FiturPaket::bersihkan((int) $data['cabang_id']);

        return back()->with('success', 'Langganan cabang berhasil disimpan.');
    }

    // ── Daftar tagihan ──────────────────────────────────────────────────
    public function tagihan()
    {
        $tagihan = Tagihan::with(['cabang', 'langganan.paket'])
            ->orderByDesc('id')
            ->get();

        return view('superadmin.tagihan', compact('tagihan'));
    }

    // ── Tandai tagihan lunas ────────────────────────────────────────────
    public function lunaskan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tagihan,id',
            'metode_bayar' => 'nullable|string|max:30',
        ]);

        $tagihan = Tagihan::findOrFail($request->id);

        $tagihan->update([
            'status' => 'lunas',
            'dibayar_pada' => now(),
            'metode_bayar' => $request->metode_bayar ?? 'transfer',
        ]);

        FiturPaket::bersihkan($tagihan->cabang_id);

        return back()->with('success', "Tagihan {$tagihan->nomor} ditandai lunas.");
    }

    // ── Daftar paket ────────────────────────────────────────────────────
    public function paket()
    {
        $paket = Paket::orderBy('urutan')->get();

        // jumlah cabang per paket
        $pemakai = Langganan::select('paket_id', DB::raw('count(*) as jml'))
            ->groupBy('paket_id')->pluck('jml', 'paket_id');

        return view('superadmin.paket', compact('paket', 'pemakai'));
    }
}
