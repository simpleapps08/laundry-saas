<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Merchant — pemilik bisnis di atas cabang.
 *
 * Hierarki:
 *   merchant (pemegang langganan, pemilik bisnis)
 *      └── cabang (outlet, unit isolasi data)
 *
 * Satu merchant bisa punya banyak cabang. Owner merchant (pemilik_id)
 * dapat memonitor seluruh cabang di bawahnya.
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
    public function pemilik()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    /** Paket langganan level merchant. */
    public function paket()
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

    /* ---------------- Helper ---------------- */

    /** Jumlah cabang aktif. */
    public function jumlahCabang(): int
    {
        return $this->cabangs()->count();
    }

    /** Kuota cabang dari paket; -1 = tanpa batas. */
    public function batasCabang(): int
    {
        return (int) ($this->paket->batas_cabang ?? 1);
    }

    /** Boleh menambah cabang lagi? */
    public function bolehTambahCabang(): bool
    {
        $batas = $this->batasCabang();

        return $batas === -1 || $this->jumlahCabang() < $batas;
    }
}
