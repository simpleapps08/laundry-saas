<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\PesananOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * F2 — Order Online publik (self-service, TANPA akun).
 *
 * Pelanggan mengisi form; pesanan masuk ke kotak masuk `pesanan_online`
 * untuk diproses kasir. Harga BELUM ada (belum ditimbang).
 */
class OrderOnlineController extends Controller
{
    /** Form pemesanan publik. */
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
            'cabangs'    => $cabangs,
            'cabangId'   => $cabangId,
            'modeDefault'=> $request->query('mode', 'pickup'),
        ]);
    }

    /** Terima pemesanan. */
    public function kirim(Request $request)
    {
        $data = $request->validate([
            'nama'          => 'required|string|max:100',
            'no_telp'       => 'required|string|max:20|regex:/^[0-9+\-\s()]{8,20}$/',
            'alamat'        => 'required|string|max:500',
            'cabang_id'     => 'required|exists:cabang,id',
            'mode_layanan'  => 'required|in:pickup,dropoff',
            'jenis_pakaian' => 'nullable|string|max:100',
            'estimasi_kg'   => 'nullable|numeric|min:0|max:999',
            'catatan'       => 'nullable|string|max:500',
        ], [
            'nama.required'         => 'Nama wajib diisi.',
            'no_telp.required'      => 'Nomor WhatsApp wajib diisi.',
            'no_telp.regex'         => 'Nomor WhatsApp tidak valid.',
            'alamat.required'       => 'Alamat wajib diisi.',
            'cabang_id.required'    => 'Silakan pilih cabang.',
            'cabang_id.exists'      => 'Cabang tidak ditemukan.',
            'mode_layanan.required' => 'Pilih metode: dijemput atau antar sendiri.',
        ]);

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
}
