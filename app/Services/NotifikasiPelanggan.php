<?php

namespace App\Services;

use App\Models\transaksi;
use Illuminate\Support\Facades\Log;

/**
 * F4 — NotifikasiPelanggan: satu sumber kebenaran pesan status ke pelanggan.
 *
 * Semua pesan WhatsApp per status ada di sini, bukan tersebar di controller.
 * Best-effort: gagal kirim TIDAK menggagalkan proses utama.
 */
class NotifikasiPelanggan
{
    /** Template pesan per status. {nama}, {invoice}, {cabang} akan diganti. */
    public const PESAN = [
        'Dijemput' => 'Halo Kak {nama}, cucian Anda sudah kami jemput. Invoice {invoice}. Kami kabari lagi ya :)',
        'Process'  => 'Halo Kak {nama}, cucian Anda sedang kami proses. Invoice {invoice}.',
        'Done'     => 'Halo Kak {nama}, cucian Anda sudah SELESAI. Invoice {invoice}.',
        'Diantar'  => 'Halo Kak {nama}, cucian Anda sedang DIANTAR oleh kurir kami. Mohon ditunggu ya :) Invoice {invoice}.',
        'Delivery' => 'Halo Kak {nama}, cucian Anda sudah diterima. Terima kasih sudah pakai {cabang} :)',
        'Batal'    => 'Halo Kak {nama}, pesanan {invoice} telah dibatalkan. Hubungi kami bila ada pertanyaan.',
    ];

    /**
     * Kirim notifikasi WhatsApp ke pelanggan untuk status tertentu.
     */
    public static function kirimStatus(transaksi $transaksi, string $status): bool
    {
        $pesan = self::PESAN[$status] ?? null;
        if ($pesan === null) {
            return false;
        }

        $token = getTokenWhatsapp();
        $kontak = self::kontakPelanggan($transaksi);

        if (empty($token) || empty($kontak)) {
            Log::info('NotifikasiPelanggan: dilewati (token/kontak kosong)', [
                'transaksi' => $transaksi->invoice,
                'status'    => $status,
            ]);
            return false;
        }

        $teks = str_replace(
            ['{nama}', '{invoice}', '{cabang}'],
            [
                $transaksi->customer ?: 'Pelanggan',
                $transaksi->invoice,
                optional($transaksi->cabang ?? null)->nama ?? 'Laundry kami',
            ],
            $pesan
        );

        return notificationWhatsapp($token, $kontak, $teks);
    }

    /** Nomor WA pelanggan: dari relasi customer, fallback ke users. */
    private static function kontakPelanggan(transaksi $transaksi): ?string
    {
        $cust = $transaksi->customers?->no_telp;
        if (! empty($cust)) {
            return $cust;
        }

        // Guest order online: nomor ada di pesanan_online (via transaksi_id).
        $online = \App\Models\PesananOnline::where('transaksi_id', $transaksi->id)->first();
        if ($online && ! empty($online->no_telp)) {
            return $online->no_telp;
        }

        return null;
    }
}
