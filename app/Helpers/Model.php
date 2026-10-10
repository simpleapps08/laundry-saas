<?php
use App\Models\{Notification, User,notifications_setting,transaksi};
use PhpParser\Node\Stmt\Return_;

class Rupiah {
    public static function getRupiah($value) {
        $format = "Rp " . number_format($value,0,',','.');
        return $format;
    }
}

// Get Email Customer by id
if (! function_exists('email_customer'))
{
    function email_customer($id=0)
    {
      $model = new User;
      $data  = $model::where('id',$id)->first();
      $email_customer = !empty($data) ? $data->email : 'Not Found';
      return $email_customer;
    }
}

// Get Nama Customer by id
if (! function_exists('namaCustomer'))
{
    function namaCustomer($id=0)
    {
        $model = new User;
        $data  = $model::where('id',$id)->first();
        $name = !empty($data) ? $data->name : 'Not Found';
        return $name;
    }
}

// Setting Email Notifications
if (! function_exists('setNotificationEmail'))
{
    function setNotificationEmail($id='')
    {
        $model = new notifications_setting;
        $data  = $model::where('email',$id)->first();
        $email = $data ? $data->email : 'Email Notification Aktif Tidak';
        return $email;
    }
}

// Setting Telegram Order Masuk Notifications
if (! function_exists('setNotificationTelegramIn'))
{
    function setNotificationTelegramIn($id='')
    {
        $model = new notifications_setting;
        $data  = $model::where('telegram_order_masuk',$id)->first();
        $teleIn = $data ? $data->telegram_order_masuk : 'Telegram Notification Order Masuk Tidak Aktif';
        return $teleIn;
    }
}

// Setting Telegram Order Selesai Notifications
if (! function_exists('setNotificationTelegramFinish'))
{
    function setNotificationTelegramFinish($id='')
    {
        $model = new notifications_setting;
        $data  = $model::where('telegram_order_selesai',$id)->first();
        $teleFininsh = $data ? $data->telegram_order_selesai : 'Telegram Notification Order Selesai Tidak Aktif';
        return $teleFininsh;
    }
}

// Get Telegram Channel untuk order masuk
if (! function_exists('telegram_channel_masuk'))
{
    function telegram_channel_masuk()
    {
        // FIX: dulu first() global -> ambil setelan cabang lain. Sekarang per-cabang aktif.
        $data  = notifications_setting::first();
        $channel_masuk = $data ? $data->telegram_channel_masuk : NULL;
        return $channel_masuk;
    }
}

// Get Telegram Channel untuk order selesai
if (! function_exists('telegram_channel_selesai'))
{
    function telegram_channel_selesai()
    {
        // FIX: dulu first() global. Sekarang ikut scope cabang (model MilikCabang).
        $data  = notifications_setting::first();
        $channel_selesai = $data ? $data->telegram_channel_selesai : NULL;
        return $channel_selesai;
    }
}

// Setting WhatsApp Notification order selesai
if (! function_exists('setNotificationWhatsappOrderSelesai'))
{
    function setNotificationWhatsappOrderSelesai($id='')
    {
        $model = new notifications_setting;
        $data  = $model::where('wa_order_selesai',$id)->first();
        $whatsappFinish = $data ? $data->wa_order_selesai : 'WhatsApp Notification Order Selesai Tidak Aktif';
        return $whatsappFinish;
    }
}

// Get WhatsApp Notifikasi order selesai
if (! function_exists('wa_order_selesai'))
{
    function wa_order_selesai()
    {
        // FIX: first() global -> sekarang ikut scope cabang.
        $data  = notifications_setting::first();
        $channel_selesai = $data ? $data->wa_order_selesai : NULL;
        return $channel_selesai;
    }
}

// Get Token WhatsApp
if (! function_exists('getTokenWhatsapp'))
{
    function getTokenWhatsapp()
    {
        // FIX: first() global -> sekarang ikut scope cabang.
        $data  = notifications_setting::first();
        $channel_selesai = $data ? $data->wa_token : NULL;
        return $channel_selesai;
    }
}

// Notifikasi Whatsapp
if (! function_exists('notificationWhatsapp'))
{
    function notificationWhatsapp($token,$waphone,$pesan)
    {
        // F4: notifikasi adalah BEST-EFFORT — kegagalan kirim TIDAK boleh
        // menggagalkan proses utama (ubah status / simpan order).
        if (empty($token) || empty($waphone)) {
            \Log::info('WA dilewati: token/kontak kosong', ['wa' => $waphone]);
            return false;
        }

        try {
            $apiURL = 'https://api.kirimwa.id/v1/messages';
            $device = config('services.kirimwa.device_id') ?: env('KIRIMWA_DEVICE_ID', 'iphone');

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $apiURL, [
              'headers'=> [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json'
              ],
              'body' => json_encode([
                'message' => $pesan,
                'phone_number' => $waphone,
                'message_type' => 'text',
                'device_id' => $device
              ]),
              'timeout' => 10,
              'connect_timeout' => 5,
            ]);

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (\Throwable $e) {
            \Log::warning('WA gagal kirim: ' . $e->getMessage(), ['wa' => $waphone]);
            return false;
        }
    }
}

// Get Notifikasi
function getNotifikasi($user_id)
{
    $model = new Notification;
    $data = $model::where('user_id',$user_id)->where('is_read',0)->orderBy('created_at','desc')->get();
    return $data;
}

// Send Notif
function sendNotification($id=null, $user_id=null, $kategori=null, $title=null, $body=null)
{
    $notif = new Notification;
    $notif->transaksi_id    = $id ?? null;
    $notif->user_id         = $user_id ?? null;
    $notif->kategori        = $kategori;
    $notif->title           = $title;
    $notif->body            = $body;
    $notif->save();

    return $notif;
}
