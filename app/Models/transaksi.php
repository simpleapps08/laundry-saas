<?php

namespace App\Models;

use App\Models\Concerns\MilikCabang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class transaksi extends Model
{
    use Notifiable, MilikCabang;
    protected $fillable = [
        'cabang_id','customer_id','user_id','tgl_transaksi','customer','status_order','status_payment','harga_id','kg','hari','harga','tgl','tgl_ambil','invoice','disc','bulan','tahun','harga_akhir','email_customer','jenis_pembayaran',
        'kg_numeric','harga_numeric','disc_numeric','harga_akhir_numeric','total_numeric',
        'tanggal_masuk','tanggal_ambil',
        'ongkir_numeric','jarak_km','mode_layanan','alamat_jemput','sumber_order'
    ];

    /**
     * Cast kolom numerik & tanggal hasil Fase 1.
     */
    protected $casts = [
        'kg_numeric' => 'decimal:2',
        'harga_numeric' => 'decimal:2',
        'disc_numeric' => 'decimal:2',
        'harga_akhir_numeric' => 'decimal:2',
        'total_numeric' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_ambil' => 'date',
        'ongkir_numeric' => 'decimal:2',
        'jarak_km' => 'decimal:2',
    ];

    public function price()
    {
      return $this->belongsTo(harga::class,'harga_id','id');
    }

    public function customers()
    {
      return $this->belongsTo(User::class,'customer_id','id')->where('auth','Customer');
    }

    public function user()
    {
      return $this->belongsTo(User::class,'user_id','id');
    }

}
