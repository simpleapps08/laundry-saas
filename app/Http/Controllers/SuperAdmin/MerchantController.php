<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Langganan;
use App\Models\Merchant;
use App\Models\Paket;
use App\Models\Tagihan;
use App\Models\User;
use App\Services\FiturPaket;
use App\Services\HargaLangganan;
use App\Services\OnboardingMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * MerchantController — CRUD penuh merchant dari panel super-admin.
 *
 * Sebelum Tahap 5, merchant hanya bisa dibaca (read-only). Sekarang:
 *  - index    → daftar + rekap tagihan
 *  - create   → form onboarding (merchant + owner + cabang + langganan)
 *  - store    → jalankan onboarding ATOMIK
 *  - show     → detail merchant: cabang, langganan, tagihan, rincian harga
 *  - edit     → form ubah
 *  - update   → simpan perubahan
 *  - toggleStatus → aktif / nonaktif / suspended
 *  - tambahCabang → tambah cabang dengan cek kuota
 */
class MerchantController extends Controller
{
    // ── Daftar merchant + rekap tagihan ─────────────────────────────────
    public function index()
    {
        $merchants = Merchant::with(['pemilik', 'cabangs', 'langganans.paket'])
            ->orderBy('nama')
            ->get();

        $rekap = Tagihan::select('merchant_id',
                DB::raw("SUM(CASE WHEN status IN ('belum_bayar','menunggu_verifikasi') THEN jumlah ELSE 0 END) as tertunggak"),
                DB::raw("SUM(CASE WHEN status = 'lunas' THEN jumlah ELSE 0 END) as lunas"),
                DB::raw('COUNT(*) as jml_tagihan'))
            ->groupBy('merchant_id')
            ->get()
            ->keyBy('merchant_id');

        return view('superadmin.merchant', compact('merchants', 'rekap'));
    }

    // ── Form onboarding merchant baru ───────────────────────────────────
    public function create()
    {
        $paket = Paket::aktif()->orderBy('urutan')->get();

        return view('superadmin.merchant-form', [
            'merchant' => null,
            'paket' => $paket,
        ]);
    }

    // ── Simpan merchant baru (onboarding atomik) ────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email|unique:merchant,email',
            'password' => 'required|string|min:6|confirmed',
            'owner_nama' => 'nullable|string|max:191',
            'no_telp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:191',
            'nama_cabang' => 'nullable|string|max:191',
            'paket_id' => 'required|exists:paket,id',
            'siklus' => 'required|in:bulanan,tahunan',
            'catatan' => 'nullable|string',
        ]);

        try {
            $hasil = OnboardingMerchant::buat($data);
        } catch (\Throwable $e) {
            return back()->withErrors(['nama' => 'Gagal membuat merchant: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('superadmin.merchant.show', $hasil['merchant']->id)
            ->with('success', "Merchant {$hasil['merchant']->nama} berhasil dibuat "
                . "(owner: {$hasil['owner']->email}, cabang: {$hasil['cabang']->nama}).");
    }

    // ── Detail merchant ─────────────────────────────────────────────────
    public function show(Merchant $merchant)
    {
        $merchant->load(['pemilik', 'cabangs.langganan.paket', 'langganans.paket', 'users']);

        $rincian = $merchant->rincianTagihan();

        $tagihan = Tagihan::with('cabang')
            ->where('merchant_id', $merchant->id)
            ->orderByDesc('id')
            ->get();

        $paket = Paket::aktif()->orderBy('urutan')->get();

        return view('superadmin.merchant-detail', compact(
            'merchant', 'rincian', 'tagihan', 'paket'
        ));
    }

    // ── Form ubah merchant ──────────────────────────────────────────────
    public function edit(Merchant $merchant)
    {
        $paket = Paket::aktif()->orderBy('urutan')->get();

        return view('superadmin.merchant-form', compact('merchant', 'paket'));
    }

    // ── Simpan perubahan ────────────────────────────────────────────────
    public function update(Request $request, Merchant $merchant)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:191',
            'email' => ['nullable', 'email', 'max:191',
                Rule::unique('merchant', 'email')->ignore($merchant->id)],
            'no_telp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:191',
            'status' => 'required|in:aktif,nonaktif,suspended',
            'catatan' => 'nullable|string',
        ]);

        $merchant->update($data);

        return redirect()->route('superadmin.merchant.show', $merchant->id)
            ->with('success', "Merchant {$merchant->nama} berhasil diperbarui.");
    }

    // ── Ubah status (aktif/nonaktif/suspended) ──────────────────────────
    public function toggleStatus(Request $request, Merchant $merchant)
    {
        $data = $request->validate([
            'status' => 'required|in:aktif,nonaktif,suspended',
        ]);

        $merchant->update(['status' => $data['status']]);

        // Cabang ikut status merchant supaya konsisten di seluruh aplikasi.
        $statusCabang = $data['status'] === 'aktif' ? 'aktif' : 'nonaktif';
        Cabang::where('merchant_id', $merchant->id)->update(['status' => $statusCabang]);

        FiturPaket::bersihkan(null);

        return back()->with('success', "Status merchant {$merchant->nama} diubah ke "
            . strtoupper($data['status']) . '.');
    }

    // ── Tambah cabang (cek kuota paket) ─────────────────────────────────
    public function tambahCabang(Request $request, Merchant $merchant)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'no_telp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:191',
            'siklus' => 'nullable|in:bulanan,tahunan',
        ]);

        try {
            $cabang = OnboardingMerchant::tambahCabang($merchant, $data);
        } catch (\Throwable $e) {
            return back()->withErrors(['nama' => $e->getMessage()]);
        }

        return back()->with('success', "Cabang {$cabang->nama} berhasil ditambahkan.");
    }

    // ── Generate tagihan utk semua langganan merchant ───────────────────
    public function buatTagihan(Request $request, Merchant $merchant)
    {
        $rincian = $merchant->rincianTagihan();

        if (empty($rincian['baris'])) {
            return back()->with('error', 'Merchant ini belum punya langganan aktif.');
        }

        $dibuat = 0;
        foreach ($rincian['baris'] as $b) {
            $l = Langganan::find($b['langganan_id']);
            if (! $l) {
                continue;
            }

            $mulai = now()->toDateString();
            $akhir = $l->siklus === 'tahunan'
                ? now()->addYear()->toDateString()
                : now()->addMonth()->toDateString();

            Tagihan::create([
                'langganan_id' => $l->id,
                'cabang_id' => $l->cabang_id,
                'merchant_id' => $merchant->id,
                'nomor' => Tagihan::buatNomor(),
                'jumlah' => $b['total'],
                'status' => 'belum_bayar',
                'jatuh_tempo' => now()->addDays(7)->toDateString(),
                'periode_mulai' => $mulai,
                'periode_akhir' => $akhir,
                'catatan' => "Diskon {$rincian['diskon_persen']}% ({$rincian['jumlah_cabang']} cabang).",
            ]);

            $dibuat++;
        }

        return back()->with('success', "{$dibuat} tagihan dibuat untuk {$merchant->nama} "
            . '(total Rp ' . number_format($rincian['total'], 0, ',', '.') . ').');
    }

    // ── Ringkasan tagihan per merchant (utk halaman index) ──────────────
    public function daftarTagihan()
    {
        $tagihan = Tagihan::with(['cabang', 'merchant', 'langganan.paket'])
            ->orderByDesc('id')
            ->get();

        return view('superadmin.tagihan', compact('tagihan'));
    }
}
