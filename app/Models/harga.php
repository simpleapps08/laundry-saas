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
}
