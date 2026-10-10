<?php

namespace App\Models;

use App\Models\Concerns\MilikCabang;
use Illuminate\Database\Eloquent\Model;

class harga extends Model
{
    use MilikCabang;

    protected $fillable = [
        'cabang_id','user_id','jenis','kg','harga','status','hari',
        'kg_numeric','harga_numeric'
    ];

    protected $casts = [
        'kg_numeric' => 'decimal:2',
        'harga_numeric' => 'decimal:2',
    ];

    public function transaksi()
    {
      return $this->hasMany(transaksi::class);
    }

    public function harga_user()
    {
      return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * Cabang pemilik harga ini (sumber kebenaran nama cabang).
     * Kolom cabang_id sudah terisi untuk semua data; relasi ke User di atas
     * hanya cadangan untuk data lama yang belum punya cabang_id.
     */
    public function cabang()
    {
      return $this->belongsTo(\App\Models\Cabang::class, 'cabang_id', 'id');
    }

    /**
     * Nama cabang untuk ditampilkan.
     * Prioritas: tabel cabang (cabang_id) -> fallback kolom users.nama_cabang (data lama).
     */
    public function getNamaCabangTampilAttribute(): string
    {
      return $this->cabang->nama
          ?? $this->harga_user->nama_cabang
          ?? '—';
    }
}
