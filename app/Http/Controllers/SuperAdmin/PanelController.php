<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DiskonLangganan;
use App\Models\Langganan;
use App\Models\Merchant;
use App\Models\Paket;
use App\Models\Tagihan;
use App\Models\transaksi;
use App\Models\User;
use App\Services\FiturPaket;
use App\Services\HargaLangganan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Panel Super-Admin — mengelola seluruh cabang, merchant, langganan & tagihan.
 *
 * Tahap 4: langganan naik ke level merchant. Halaman baru:
 *  - setelan()       → atur diskon bertingkat (tabel diskon_langganan)
 *  - merchant()      → daftar merchant + rekap tagihan (per cabang, kena diskon)
 *  - buatTagihanMerchant() → generate tagihan utk semua langganan merchant
 */
class PanelController extends Controller
{
    // ── Dashboard ───────────────────────────────────────────────────────
    public function index()
    {
        $totalCabang = \App\Models\Cabang::count();
        $cabangAktif = \App\Models\Cabang::where('status', 'aktif')->count();
        $totalMerchant = Merchant::count();
        $totalUser = User::withoutGlobalScope('cabang')->count();

        $totalPendapatan = transaksi::tanpaScopeCabang()
            ->where('status_payment', 'Success')
            ->sum('harga_akhir_numeric');

        $pendapatanBulan = transaksi::tanpaScopeCabang()
            ->where('status_payment', 'Success')
            ->whereYear('tanggal_masuk', date('Y'))
            ->whereMonth('tanggal_masuk', date('m'))
            ->sum('harga_akhir_numeric');

        // Langganan perlu perhatian
        $akanBerakhir = Langganan::with(['cabang', 'merchant', 'paket'])
            ->whereIn('status', ['trial', 'aktif'])
            ->whereNotNull('berakhir')
            ->whereBetween('berakhir', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('berakhir')
            ->get();

        $jatuhTempo = Langganan::with(['cabang', 'merchant', 'paket'])
            ->where('status', 'jatuh_tempo')
            ->orderBy('berakhir')
            ->get();

        $tagihanTertunggak = Tagihan::with(['cabang', 'merchant'])
            ->belumBayar()
            ->orderBy('jatuh_tempo')
            ->limit(20)
            ->get();

        $nilaiTertunggak = Tagihan::belumBayar()->sum('jumlah');

        // MRR = jumlah harga langganan aktif setelah diskon bertingkat per merchant
        $mrr = 0.0;
        foreach (Merchant::all() as $m) {
            $mrr += (float) $m->rincianTagihan()['total'];
        }

        return view('superadmin.index', compact(
            'totalCabang', 'cabangAktif', 'totalMerchant', 'totalUser',
            'totalPendapatan', 'pendapatanBulan',
            'akanBerakhir', 'jatuhTempo', 'tagihanTertunggak', 'nilaiTertunggak',
            'mrr'
        ));
    }

    // ── Daftar cabang ───────────────────────────────────────────────────
    public function cabang()
    {
        $cabang = \App\Models\Cabang::withCount(['users as jumlah_user'])
          ->with(['merchant', 'langganan.paket'])
          ->orderBy('nama')
          ->get();

        $stat = transaksi::tanpaScopeCabang()
            ->select('cabang_id',
                DB::raw('count(*) as jml'),
                DB::raw('COALESCE(SUM(harga_akhir_numeric),0) as total'))
            ->groupBy('cabang_id')
            ->get()
            ->keyBy('cabang_id');

        return view('superadmin.cabang', compact('cabang', 'stat'));
    }

    // ── Daftar merchant + rekap tagihan ─────────────────────────────────
    public function merchant()
    {
        $merchants = Merchant::with(['pemilik', 'cabangs', 'langganans.paket'])
            ->orderBy('nama')
            ->get();

        // rekap tagihan per merchant
        $rekap = Tagihan::select('merchant_id',
                DB::raw("SUM(CASE WHEN status IN ('belum_bayar','menunggu_verifikasi') THEN jumlah ELSE 0 END) as tertunggak"),
                DB::raw("SUM(CASE WHEN status = 'lunas' THEN jumlah ELSE 0 END) as lunas"),
                DB::raw('COUNT(*) as jml_tagihan'))
            ->groupBy('merchant_id')
            ->get()
            ->keyBy('merchant_id');

        return view('superadmin.merchant', compact('merchants', 'rekap'));
    }

    // ── Setelan diskon bertingkat ───────────────────────────────────────
    public function setelan()
    {
        $diskon = DiskonLangganan::orderBy('min_cabang')->get();
        $paket = Paket::orderBy('urutan')->get();

        // pratinjau harga per jumlah cabang untuk tiap paket
        $pratinjau = [];
        foreach ($paket as $p) {
            foreach ([1, 2, 3, 4, 5] as $jml) {
                $pratinjau[$p->id][$jml] = HargaLangganan::hargaPaket($p, 'bulanan', $jml);
            }
        }

        return view('superadmin.setelan', compact('diskon', 'paket', 'pratinjau'));
    }

    // ── Simpan setelan diskon ───────────────────────────────────────────
    public function simpanSetelan(Request $request)
    {
        $data = $request->validate([
            'diskon' => 'required|array',
            'diskon.*.id' => 'required|exists:diskon_langganan,id',
            'diskon.*.min_cabang' => 'required|integer|min:1',
            'diskon.*.diskon_persen' => 'required|numeric|min:0|max:100',
            'diskon.*.label' => 'nullable|string|max:60',
            'diskon.*.is_aktif' => 'nullable|boolean',
        ]);

        // Cek duplikat min_cabang
        $mins = collect($data['diskon'])->pluck('min_cabang')->map(fn ($v) => (int) $v);
        if ($mins->count() !== $mins->unique()->count()) {
            return back()->withErrors([
                'diskon' => 'Ada tingkatan dengan jumlah cabang minimum yang sama. Perbaiki dulu.',
            ])->withInput();
        }

        foreach ($data['diskon'] as $d) {
            DiskonLangganan::where('id', $d['id'])->update([
                'min_cabang' => (int) $d['min_cabang'],
                'diskon_persen' => (float) $d['diskon_persen'],
                'label' => $d['label'] ?? null,
                'is_aktif' => ! empty($d['is_aktif']),
            ]);
        }

        DiskonLangganan::bersihkanSemuaCache();
        Cache::flush();

        return back()->with('success', 'Setelan diskon bertingkat berhasil disimpan.');
    }

    // ── Daftar langganan ────────────────────────────────────────────────
    public function langganan()
    {
        $langganan = Langganan::with(['cabang', 'merchant', 'paket'])
            ->orderByDesc('id')
            ->get();

        $paket = Paket::aktif()->get();
        $merchant = Merchant::orderBy('nama')->get();
        $cabang = \App\Models\Cabang::orderBy('nama')->get();

        return view('superadmin.langganan', compact('langganan', 'paket', 'merchant', 'cabang'));
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
            'harga_disepakati' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        // merchant_id diturunkan dari cabang (satu sumber kebenaran)
        $data['merchant_id'] = \App\Models\Cabang::where('id', $data['cabang_id'])->value('merchant_id');
        $data['jumlah_cabang'] = Langganan::where('merchant_id', $data['merchant_id'])
            ->whereIn('status', ['trial', 'aktif'])
            ->count() ?: 1;

        Langganan::updateOrCreate(
            ['cabang_id' => $data['cabang_id']],
            $data
        );

        FiturPaket::bersihkan((int) $data['cabang_id']);
        DiskonLangganan::bersihkanSemuaCache();

        return back()->with('success', 'Langganan cabang berhasil disimpan.');
    }

    // ── Daftar tagihan ──────────────────────────────────────────────────
    public function tagihan()
    {
        $tagihan = Tagihan::with(['cabang', 'merchant', 'langganan.paket'])
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

    // ── Generate tagihan utk semua langganan merchant ───────────────────
    public function buatTagihanMerchant(Request $request)
    {
        $data = $request->validate([
            'merchant_id' => 'required|exists:merchant,id',
        ]);

        $merchant = Merchant::findOrFail($data['merchant_id']);
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

        return back()->with('success', "{$dibuat} tagihan dibuat untuk {$merchant->nama} (total Rp "
            . number_format($rincian['total'], 0, ',', '.') . ').');
    }

    // ── Daftar paket ────────────────────────────────────────────────────
    public function paket()
    {
        $paket = Paket::orderBy('urutan')->get();

        // jumlah langganan per paket
        $pemakai = Langganan::select('paket_id', DB::raw('count(*) as jml'))
            ->groupBy('paket_id')->pluck('jml', 'paket_id');

        return view('superadmin.paket', compact('paket', 'pemakai'));
    }
}
