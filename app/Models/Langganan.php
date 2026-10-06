<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Langganan satu cabang.
 *
 * TIDAK memakai SoftDeletes — riwayat langganan harus tetap ada
 * untuk keperluan audit pendapatan.
 */
class Langganan extends Model
{
    use HasFactory;

    protected $table = 'langganan';

    protected $fillable = [
        'cabang_id', 'paket_id', 'siklus', 'status',
        'mulai', 'berakhir', 'trial_berakhir',
        'harga_disepakati', 'catatan',
    ];

    protected $casts = [
        'mulai' => 'date',
        'berakhir' => 'date',
        'trial_berakhir' => 'date',
        'harga_disepakati' => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'langganan_id');
    }

    // ── Scope ───────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['trial', 'aktif']);
    }

    // ── Helper status ───────────────────────────────────────────────────

    public function masihBerlaku(): bool
    {
        if (! in_array($this->status, ['trial', 'aktif'])) {
            return false;
        }

        return $this->berakhir === null || $this->berakhir->isFuture();
    }

    public function sisaHari(): int
    {
        if ($this->berakhir === null) {
            return -1;
        }

        return (int) Carbon::now()->startOfDay()
            ->diffInDays($this->berakhir->startOfDay(), false);
    }

    /**
     * Segarkan status berdasarkan tanggal berakhir.
     * Dipanggil oleh scheduled command (lihat RefreshStatusLangganan).
     */
    public function segarkanStatus(): self
    {
        if (in_array($this->status, ['berhenti'])) {
            return $this;
        }

        if ($this->berakhir !== null && $this->berakhir->isPast()) {
            $this->status = 'jatuh_tempo';
        } elseif ($this->status === 'trial' && $this->masihBerlaku()) {
            // tetap trial
        } elseif ($this->masihBerlaku()) {
            $this->status = 'aktif';
        }

        $this->save();

        return $this;
    }

    public function hargaEfektif(): float
    {
        return (float) ($this->harga_disepakati
            ?? $this->paket?->hargaUntuk($this->siklus)
            ?? 0);
    }
}
