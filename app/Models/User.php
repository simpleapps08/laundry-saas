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
        'cabang_id','merchant_id','karyawan_id','name', 'email', 'password','status','auth','nama_cabang','alamat_cabang','alamat','no_telp','theme','foto','point'
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
     * Merchant tempat user ini bernaung.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    /**
     * Semua cabang yang boleh diakses user ini (many-to-many).
     *
     * Owner merchant melihat seluruh cabang di bawah merchant-nya.
     */
    public function cabangs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Cabang::class, 'cabang_user', 'user_id', 'cabang_id')
            ->withPivot('peran_di_cabang')
            ->withTimestamps();
    }

    /**
     * Daftar ID cabang yang boleh diakses user ini.
     * Dipakai untuk scope monitoring lintas cabang.
     *
     * @return array<int>
     */
    public function cabangIds(): array
    {
        // 1. Super-admin (tanpa merchant & tanpa cabang) -> SEMUA cabang
        if ($this->isSuperAdmin()) {
            return Cabang::pluck('id')->all();
        }

        // 2. User ber-merchant -> HANYA cabang milik merchant-nya.
        //    Ini yang mencegah owner merchant A melihat cabang merchant B.
        if ($this->merchant_id !== null) {
            return Cabang::where('merchant_id', $this->merchant_id)
                ->pluck('id')->map('intval')->all();
        }

        // 3. User tanpa merchant (data lama) -> cabang dari pivot + cabang utama
        $ids = $this->cabangs()->pluck('cabang.id')->map('intval')->all();

        if ($this->cabang_id !== null && ! in_array((int) $this->cabang_id, $ids, true)) {
            $ids[] = (int) $this->cabang_id;
        }

        return array_values(array_unique($ids));
    }

    /**
     * Boleh mengakses cabang tertentu?
     */
    public function bolehAksesCabang($cabangId): bool
    {
        return in_array((int) $cabangId, array_map('intval', $this->cabangIds()), true);
    }

    /**
     * Mengelola lebih dari satu cabang? (kandidat owner multi-cabang)
     */
    public function multiCabang(): bool
    {
        return count($this->cabangIds()) > 1;
    }

    /**
     * True kalau user ini super-admin (tidak terikat cabang).
     */
    public function isSuperAdmin(): bool
    {
        // Sumber kebenaran tunggal untuk "super-admin".
        //
        // ⚠️ PENTING (revisi setelah ada tingkatan merchant):
        // Sejak `merchant` ditambahkan, `cabang_id === null` TIDAK LAGI cukup
        // menandai super-admin — karena OWNER MERCHANT juga `cabang_id === null`
        // (mereka tidak terikat pada satu cabang). Kalau tidak dibedakan,
        // owner merchant akan dianggap super-admin dan bisa melihat SEMUA
        // cabang milik merchant lain (kebocoran isolasi).
        //
        // Super-admin = berperan 'SuperAdmin'
        //               ATAU (tanpa merchant DAN tanpa cabang) -> Javacom pusat.
        if ($this->hasRole('SuperAdmin')) {
            return true;
        }

        return $this->merchant_id === null && $this->cabang_id === null;
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
