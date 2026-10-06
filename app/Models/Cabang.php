<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cabang = entitas penyewa (tenant).
 *
 * Satu cabang bisa punya banyak user (admin, karyawan, customer),
 * banyak transaksi, dan punya daftar harga sendiri.
 */
class Cabang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cabang';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'no_telp',
        'email',
        'pemilik_id',
        'status',
        'logo',
        'catatan_kaki',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(transaksi::class, 'cabang_id');
    }

    public function hargas(): HasMany
    {
        return $this->hasMany(harga::class, 'cabang_id');
    }

    // ── Scope ───────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // ── Helper ──────────────────────────────────────────────────────────

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
