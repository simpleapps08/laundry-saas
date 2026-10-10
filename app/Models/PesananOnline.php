<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PesananOnline — kotak masuk order online (self-service tanpa akun).
 *
 * STAGING saja. Setelah kasir memproses, data mengalir ke `transaksi`
 * (sumber kebenaran sebenarnya). Lihat migration untuk alasannya.
 */
class PesananOnline extends Model
{
    protected $table = 'pesanan_online';

    protected $fillable = [
        'cabang_id', 'merchant_id',
        'nama', 'no_telp', 'alamat',
        'mode_layanan', 'jenis_pakaian', 'estimasi_kg', 'catatan',
        'status_online', 'kode_pesanan', 'transaksi_id', 'diproses_at',
    ];

    protected $casts = [
        'diproses_at' => 'datetime',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(transaksi::class, 'transaksi_id');
    }

    /** Nomor telepon dinormalkan ke format 62xxx (seperti store customer). */
    public static function normalisasiTelp(string $telp): string
    {
        $t = preg_replace('/[^0-9]/', '', $telp);

        return preg_replace('/^0/', '62', $t);
    }

    /** Kode pesanan unik untuk cek status tanpa login. */
    public static function buatKode(): string
    {
        do {
            $kode = 'PO-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (self::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}
