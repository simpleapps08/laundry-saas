<?php

namespace App\Models;

use App\Services\HargaLangganan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Merchant — pemilik bisnis di atas cabang.
 *
 * Hierarki:
 *   merchant (pemegang langganan, pemilik bisnis)
 *      └── cabang (outlet, unit isolasi data)
 *
 * Sejak Tahap 4:
 *  - `merchant.paket_id` = CERMIN dari paket tertinggi di antara langganan
 *    merchant (bukan sumber kebenaran lagi).
 *  - SUMBER KEBENARAN paket & harga = tabel `langganan` (per cabang).
 *  - Kuota cabang dihitung dari paket tertinggi tsb.
 */
class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'merchant';

    protected $fillable = [
        'kode', 'nama', 'slug', 'pemilik_id', 'paket_id',
        'email', 'no_telp', 'alamat', 'logo', 'status', 'catatan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ---------------- Relasi ---------------- */

    /** Pemilik (owner) merchant. */
    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    /** Paket langganan level merchant (cermin — lihat paketEfektif()). */
    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    /** Semua cabang di bawah merchant ini. */
    public function cabangs()
    {
        return $this->hasMany(Cabang::class, 'merchant_id');
    }

    /** Semua user yang berafiliasi merchant ini. */
    public function users()
    {
        return $this->hasMany(User::class, 'merchant_id');
    }

    /** Semua langganan merchant (per cabang). */
    public function langganans()
    {
        return $this->hasMany(Langganan::class, 'merchant_id');
    }

    /* ---------------- Helper ---------------- */

    /**
     * Paket EFEKTIF merchant = paket tertinggi dari langganan aktifnya.
     * Fallback ke kolom `paket_id` kalau belum ada langganan (data lama).
     */
    public function paketEfektif(): ?Paket
    {
        return HargaLangganan::paketTertinggi($this)
            ?? $this->paket;
    }

    /** Jumlah cabang. */
    public function jumlahCabang(): int
    {
        return $this->cabangs()->count();
    }

    /** Jumlah langganan aktif (basis diskon). */
    public function jumlahLanggananAktif(): int
    {
        return $this->langganans()->whereIn('status', ['trial', 'aktif'])->count();
    }

    /** Kuota cabang dari paket efektif; -1 = tanpa batas. */
    public function batasCabang(): int
    {
        return (int) ($this->paketEfektif()->batas_cabang ?? 1);
    }

    /** Boleh menambah cabang lagi? */
    public function bolehTambahCabang(): bool
    {
        $batas = $this->batasCabang();

        return $batas === -1 || $this->jumlahCabang() < $batas;
    }

    /** Diskon berlaku untuk merchant ini (persen). */
    public function diskonPersen(): float
    {
        return HargaLangganan::persenMerchant($this);
    }

    /** Rincian tagihan merchant (semua cabang, sudah kena diskon). */
    public function rincianTagihan(): array
    {
        return HargaLangganan::hitungMerchant($this);
    }
}
