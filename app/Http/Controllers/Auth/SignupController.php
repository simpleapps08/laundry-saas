<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Paket;
use App\Services\HargaLangganan;
use App\Services\OnboardingMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

/**
 * SignupController — pendaftaran mandiri (self-service) untuk merchant baru.
 *
 * Alur klien:
 *   /daftar            → halaman harga (landing) — pilih paket
 *   /daftar/form?paket=pro → form pendaftaran
 *   POST /daftar       → onboarding atomik + auto-login → dashboard
 *
 * ATURAN PENTING:
 *  - Halaman ini PUBLIK. Jangan pernah bocorkan data tenant di sini.
 *  - Semua merchant baru mulai sebagai TRIAL (bukan aktif berbayar).
 *  - Pendaftaran = pembuatan akun nyata → WAJIB rate-limited (anti-spam).
 */
class SignupController extends Controller
{
    /** Batas percobaan pendaftaran per IP per jam. */
    private const BATAS_DAFTAR_PER_JAM = 5;

    // ── Halaman harga / landing pendaftaran ─────────────────────────────
    public function harga()
    {
        $paket = Paket::aktif()->orderBy('urutan')->get();

        // Pratinjau harga bertingkat (1, 2, 3 cabang) untuk tiap paket,
        // supaya calon klien lihat manfaat paket multi-cabang.
        $pratinjau = [];
        foreach ($paket as $p) {
            foreach ([1, 2, 3] as $jml) {
                $pratinjau[$p->id][$jml] = HargaLangganan::hargaPaket($p, 'bulanan', $jml);
            }
        }

        return view('signup.harga', compact('paket', 'pratinjau'));
    }

    // ── Form pendaftaran ────────────────────────────────────────────────
    public function form(Request $request)
    {
        $paket = Paket::aktif()->orderBy('urutan')->get();

        if ($paket->isEmpty()) {
            return redirect()->route('signup.harga')
                ->with('error', 'Belum ada paket yang tersedia. Hubungi administrator.');
        }

        // Paket terpilih dari query string (?paket=pro), fallback ke pertama.
        $terpilih = null;
        if ($request->filled('paket')) {
            $terpilih = $paket->firstWhere('kode', $request->query('paket'));
        }
        $terpilih ??= $paket->first();

        return view('signup.form', compact('paket', 'terpilih'));
    }

    // ── Proses pendaftaran ──────────────────────────────────────────────
    public function daftar(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:191',
            'owner_nama' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email|unique:merchant,email',
            'no_telp' => 'required|string|max:30',
            'alamat' => 'nullable|string|max:191',
            'nama_cabang' => 'nullable|string|max:191',
            'paket_id' => 'required|exists:paket,id',
            'siklus' => 'required|in:bulanan,tahunan',
            'password' => ['required', 'confirmed', Password::min(8)],
            'setuju' => 'accepted',
        ], [
            'setuju.accepted' => 'Anda harus menyetujui syarat & ketentuan.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk atau pakai email lain.',
        ]);

        // Pastikan paketnya memang aktif (cegah kirim paket_id sembarangan).
        $paket = Paket::aktif()->find($data['paket_id']);
        if (! $paket) {
            return back()->withErrors(['paket_id' => 'Paket tidak tersedia.'])->withInput();
        }

        try {
            $hasil = OnboardingMerchant::buat([
                'nama' => $data['nama'],
                'owner_nama' => $data['owner_nama'],
                'email' => $data['email'],
                'password' => $data['password'],
                'no_telp' => $data['no_telp'],
                'alamat' => $data['alamat'] ?? null,
                'nama_cabang' => $data['nama_cabang'] ?? null,
                'paket_id' => $paket->id,
                'siklus' => $data['siklus'],
                'catatan' => 'Pendaftaran mandiri (self-service signup).',
            ]);
        } catch (\Throwable $e) {
            Log::error('Signup gagal: ' . $e->getMessage(), ['email' => $data['email']]);

            return back()->withErrors([
                'email' => 'Pendaftaran gagal diproses. Silakan coba lagi atau hubungi kami.',
            ])->withInput();
        }

        // Auto-login supaya klien langsung masuk tanpa hambatan.
        Auth::login($hasil['owner']);
        $request->session()->regenerate();

        return redirect()->route('merchant.dashboard')
            ->with('success', "Selamat datang, {$hasil['merchant']->nama}! "
                . 'Akun trial Anda aktif 14 hari dengan paket ' . $paket->nama . '.');
    }
}
