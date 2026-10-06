<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cabang_id','karyawan_id','name', 'email', 'password','status','auth','nama_cabang','alamat_cabang','alamat','no_telp','theme','foto','point'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Cabang tempat user ini bernaung.
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /**
     * True kalau user ini super-admin (tidak terikat cabang).
     */
    public function isSuperAdmin(): bool
    {
        // Sumber kebenaran tunggal untuk "super-admin":
        // berperan SuperAdmin, ATAU Admin tanpa cabang (cabang_id NULL).
        // Kolom enum `auth` sengaja TIDAK dipakai lagi di sini supaya
        // tidak ada dua sumber kebenaran.
        return $this->hasRole('SuperAdmin') || $this->cabang_id === null;
    }

    /**
     * Langganan aktif user ini (lewat cabangnya).
     */
    public function langganan()
    {
        return $this->cabang?->langgananAktif();
    }

    /**
     * Cek fitur paket untuk user ini. Super-admin selalu boleh.
     */
    public function punyaFitur(string $fitur): bool
    {
        return \App\Services\FiturPaket::punya($fitur, $this->cabang_id);
    }

    function bank()
    {
      return $this->hasOne(DataBank::class);
    }

    public function transaksi()
    {
      return $this->belongsTo(transaksi::class,'id','user_id');
    }

    public function transaksiCustomer()
    {
      return $this->hasMany(transaksi::class,'customer_id','id');
    }
}
