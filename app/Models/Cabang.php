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
        'merchant_id',
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

    /**
     * Merchant pemilik cabang ini (tingkat di atas cabang).
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    /**
     * User yang punya akses ke cabang ini (many-to-many via cabang_user).
     */
    public function penggunaAkses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cabang_user', 'cabang_id', 'user_id')
            ->withPivot('peran_di_cabang')
            ->withTimestamps();
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

    public function langganan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Langganan::class, 'cabang_id')->orderByDesc('id');
    }

    /**
     * Langganan yang sedang berjalan (trial/aktif dan belum berakhir).
     */
    public function langgananAktif(): ?Langganan
    {
        return $this->langganan()->whereIn('status', ['trial', 'aktif'])
            ->where(function ($q) {
                $q->whereNull('berakhir')->orWhere('berakhir', '>=', now()->toDateString());
            })
            ->first();
    }

    public function punyaFitur(string $fitur): bool
    {
        return (bool) $this->langgananAktif()?->paket?->punya($fitur);
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
