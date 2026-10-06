<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Paket berlangganan SaaS.
 *
 * Fitur disimpan sebagai JSON supaya paket baru tidak butuh migration.
 * Akses kunci fitur lewat punya()/batas().
 */
class Paket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paket';

    protected $fillable = [
        'kode', 'nama', 'deskripsi',
        'harga_bulanan', 'harga_tahunan',
        'batas_cabang', 'batas_user', 'batas_transaksi_bulanan',
        'fitur', 'is_aktif', 'urutan',
    ];

    protected $casts = [
        'fitur' => 'array',
        'is_aktif' => 'boolean',
        'harga_bulanan' => 'decimal:2',
        'harga_tahunan' => 'decimal:2',
    ];

    public function langganan(): HasMany
    {
        return $this->hasMany(Langganan::class, 'paket_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->orderBy('urutan');
    }

    /**
     * Apakah paket ini mengaktifkan fitur tertentu?
     * Fitur yang tidak terdaftar dianggap TIDAK tersedia (default aman).
     */
    public function punya(string $fitur): bool
    {
        return (bool) ($this->fitur[$fitur] ?? false);
    }

    /**
     * Batas penggunaan. -1 = tanpa batas.
     */
    public function batas(string $jenis): int
    {
        return (int) match ($jenis) {
            'cabang' => $this->batas_cabang,
            'user' => $this->batas_user,
            'transaksi' => $this->batas_transaksi_bulanan,
            default => 0,
        };
    }

    public function tanpaBatas(string $jenis): bool
    {
        return $this->batas($jenis) === -1;
    }

    public function hargaUntuk(string $siklus): float
    {
        return (float) ($siklus === 'tahunan' ? $this->harga_tahunan : $this->harga_bulanan);
    }
}
