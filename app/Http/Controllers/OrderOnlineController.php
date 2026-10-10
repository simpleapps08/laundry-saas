<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Merchant;
use App\Models\PesananOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * F2 — Order Online publik (self-service, TANPA akun).
 *
 * Q3=C (10-Okt-2026): ditambah routing MULTI-TENANT per merchant & per cabang.
 *
 *   /pesan                          -> global (fallback, semua cabang)
 *   /{slug}/pesan                   -> merchant tertentu (cabang terbatas)
 *   /{slug}/{kode-cabang}/pesan     -> cabang spesifik (untuk QR outlet)
 *   /{slug}                         -> halaman profil/brand merchant
 *
 * Prinsip: pesanan TIDAK diarahkan lewat dropdown global lintas merchant —
 * pelanggan masuk lewat URL milik merchant itu, sehingga cabang yang tampil
 * hanya cabang merchant tsb (mencegah salah kirim ke laundry lain).
 */
class OrderOnlineController extends Controller
{
    /** Form pemesanan publik (global). */
    public function form(Request $request)
    {
        $cabangId = $request->query('cabang');

        // Daftar cabang aktif untuk dipilih pelanggan.
        $cabangs = Cabang::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'alamat']);

        // Kalau hanya 1 cabang -> pilih otomatis (kurangi friksi).
        if ($cabangs->count() === 1) {
            $cabangId = $cabangs->first()->id;
        }

        return view('order-online.form', [
            'cabangs'     => $cabangs,
            'cabangId'    => $cabangId,
            'modeDefault' => $request->query('mode', 'pickup'),
            'merchant'    => null,
            'cabangFixed' => null,
            'aksiKirim'   => route('order-online.kirim'),
        ]);
    }

    // =====================================================================
    // Q3=C — ROUTING PER MERCHANT
    // =====================================================================

    /**
     * /{slug}/pesan — form order untuk satu merchant.
     * Cabang yang tampil HANYA milik merchant ini.
     */
    public function formMerchant(Request $request, string $slug)
    {
        $merchant = $this->cariMerchant($slug);

        $cabangs = $this->cabangMerchant($merchant);

        // Merchant dengan 1 cabang -> auto-pilih (kurangi friksi).
        $cabangId = $request->query('cabang');
        if ($cabangs->count() === 1) {
            $cabangId = $cabangs->first()->id;
        }

        return view('order-online.form', [
            'cabangs'     => $cabangs->map(fn ($c) => (object) [
                'id' => $c->id, 'nama' => $c->nama, 'alamat' => $c->alamat,
            ]),
            'cabangId'    => $cabangId,
            'modeDefault' => $request->query('mode', 'pickup'),
            'merchant'    => $merchant,
            'cabangFixed' => null,
            'aksiKirim'   => route('order-online.merchant.kirim', ['slug' => $merchant->slug]),
        ]);
    }

    /** POST /{slug}/pesan — terima pesanan, paksa cabang milik merchant. */
    public function kirimMerchant(Request $request, string $slug)
    {
        $merchant = $this->cariMerchant($slug);
        $idCabangMerchant = $this->cabangMerchant($merchant)->pluck('id')->all();

        // Batasi cabang_id ke cabang milik merchant -> cegah kirim lintas merchant.
        $data = $this->validasiPesanan($request, $idCabangMerchant);

        return $this->simpanPesanan($request, $data);
    }

    /**
     * /{slug}/{kodeCabang}/pesan — form order cabang spesifik (QR outlet).
     * Cabang langsung terpilih; dropdown diganti info cabang (read-only).
     */
    public function formCabang(Request $request, string $slug, string $kodeCabang, ?string $unused = null)
    {
        $merchant = $this->cariMerchant($slug);
        $cabang   = $this->cariCabang($merchant, $kodeCabang);

        return view('order-online.form', [
            'cabangs'     => collect(),   // kosong -> view pakai cabangFixed
            'cabangId'    => $cabang->id,
            'modeDefault' => $request->query('mode', 'pickup'),
            'merchant'    => $merchant,
            'cabangFixed' => $cabang,
            'aksiKirim'   => route('order-online.cabang.kirim', [
                'slug' => $merchant->slug, 'kodeCabang' => $cabang->kode,
            ]),
        ]);
    }

    /** POST /{slug}/{kodeCabang}/pesan */
    public function kirimCabang(Request $request, string $slug, string $kodeCabang, ?string $unused = null)
    {
        $merchant = $this->cariMerchant($slug);
        $cabang   = $this->cariCabang($merchant, $kodeCabang);

        // Cabang SUDAH pasti dari URL -> jangan bergantung pada input
        // `cabang_id` (bisa kosong/diubah). Validasi hanya field pelanggan,
        // lalu PAKSA cabang_id ke cabang di URL.
        $data = $this->validasiPesanan($request, [], $cabang->id);

        return $this->simpanPesanan($request, $data);
    }

    /**
     * /{slug} — halaman profil merchant (brand + daftar cabang).
     */
    public function merchantProfile(string $slug)
    {
        $merchant = $this->cariMerchant($slug);
        $cabangs  = $this->cabangMerchant($merchant);

        return view('order-online.merchant', compact('merchant', 'cabangs'));
    }

    // =====================================================================
    // Halaman sukses & cek status
    // =====================================================================

    /** Halaman sukses + kode pesanan. */
    public function sukses(string $kode)
    {
        $pesanan = PesananOnline::with('cabang')->where('kode_pesanan', $kode)->firstOrFail();

        return view('order-online.sukses', compact('pesanan'));
    }

    /** Cek status pesanan pakai kode (tanpa akun). */
    public function cek(Request $request)
    {
        $kode = $request->query('kode');
        $pesanan = null;
        $tidakDitemukan = false;

        if ($kode) {
            $pesanan = PesananOnline::with(['cabang', 'transaksi'])
                ->where('kode_pesanan', trim($kode))
                ->first();
            $tidakDitemukan = $pesanan === null;
        }

        return view('order-online.cek', compact('pesanan', 'kode', 'tidakDitemukan'));
    }

    // =====================================================================
    // HELPER
    // =====================================================================

    /** Cari merchant aktif berdasarkan slug; 404 bila tidak ada/nonaktif. */
    private function cariMerchant(string $slug): Merchant
    {
        $merchant = Merchant::where('slug', $slug)->where('status', 'aktif')->first();
        abort_if(! $merchant, 404, 'Merchant tidak ditemukan.');

        return $merchant;
    }

    /** Cabang aktif milik merchant (urut nama). */
    private function cabangMerchant(Merchant $merchant)
    {
        return Cabang::where('merchant_id', $merchant->id)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'alamat', 'kode']);
    }

    /** Cari cabang merchant berdasarkan kode (case-insensitive); 404 bila tidak ada. */
    private function cariCabang(Merchant $merchant, string $kodeCabang): Cabang
    {
        $cabang = Cabang::where('merchant_id', $merchant->id)
            ->where('status', 'aktif')
            ->whereRaw('LOWER(kode) = ?', [mb_strtolower($kodeCabang)])
            ->first();

        abort_if(! $cabang, 404, 'Cabang tidak ditemukan.');

        return $cabang;
    }

    /**
     * Validasi input pesanan. Bila $batasCabangId diberikan, cabang_id WAJIB
     * ada di daftar itu (cegah pemesanan lintas merchant).
     */
    private function validasiPesanan(Request $request, array $batasCabangId = [], ?int $paksaCabangId = null): array
    {
        $aturan = [
            'nama'          => 'required|string|max:100',
            'no_telp'       => 'required|string|max:20|regex:/^[0-9+\-\s()]{8,20}$/',
            'alamat'        => 'required|string|max:500',
            'cabang_id'     => 'required',
            'mode_layanan'  => 'required|in:pickup,dropoff',
            'jenis_pakaian' => 'nullable|string|max:100',
            'estimasi_kg'   => 'nullable|numeric|min:0|max:999',
            'catatan'       => 'nullable|string|max:500',
        ];

        // Kalau cabang DIPAKSA dari URL (QR outlet), jangan validasi `cabang_id`
        // dari input sama sekali — cukup pasang nilainya langsung.
        if ($paksaCabangId !== null) {
            $aturan['cabang_id'] = 'nullable';
        } elseif (! empty($batasCabangId)) {
            // Terbatas ke merchant: cabang_id wajib milik merchant ini.
            $aturan['cabang_id'] .= '|integer|in:'.implode(',', $batasCabangId);
        } else {
            $aturan['cabang_id'] .= '|exists:cabang,id';
        }

        $data = $request->validate($aturan, [
            'nama.required'         => 'Nama wajib diisi.',
            'no_telp.required'      => 'Nomor WhatsApp wajib diisi.',
            'no_telp.regex'         => 'Nomor WhatsApp tidak valid.',
            'alamat.required'       => 'Alamat wajib diisi.',
            'cabang_id.required'    => 'Silakan pilih cabang.',
            'cabang_id.exists'      => 'Cabang tidak ditemukan.',
            'cabang_id.in'          => 'Cabang tidak sesuai merchant ini.',
            'mode_layanan.required' => 'Pilih metode: dijemput atau antar sendiri.',
        ]);

        if ($paksaCabangId !== null) {
            $data['cabang_id'] = $paksaCabangId;
        }

        return $data;
    }

    /** Simpan pesanan (logika sama untuk semua jalur). */
    private function simpanPesanan(Request $request, array $data)
    {
        try {
            $cabang = Cabang::find($data['cabang_id']);
            if (! $cabang) {
                return back()->withErrors(['cabang_id' => 'Cabang tidak tersedia.'])->withInput();
            }

            $pesanan = PesananOnline::create([
                'cabang_id'     => $cabang->id,
                'merchant_id'   => $cabang->merchant_id,
                'nama'          => $data['nama'],
                'no_telp'       => PesananOnline::normalisasiTelp($data['no_telp']),
                'alamat'        => $data['alamat'],
                'mode_layanan'  => $data['mode_layanan'],
                'jenis_pakaian' => $data['jenis_pakaian'] ?? null,
                'estimasi_kg'   => $data['estimasi_kg'] ?? null,
                'catatan'       => $data['catatan'] ?? null,
                'status_online' => 'Menunggu',
                'kode_pesanan'  => PesananOnline::buatKode(),
            ]);

            return redirect()
                ->route('order-online.sukses', ['kode' => $pesanan->kode_pesanan])
                ->with('sukses', 'Pesanan diterima!');
        } catch (\Throwable $e) {
            Log::error('Order online gagal: ' . $e->getMessage());
            return back()->withErrors(['umum' => 'Maaf, pesanan gagal diproses. Silakan coba lagi.'])->withInput();
        }
    }
}
