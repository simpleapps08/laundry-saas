<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'status_payment'    => 'required',
          'kg'                => 'required|regex:/^[0-9.]+$/|numeric',
          'hari'              => 'required',
          'harga'             => 'required',
          'jenis_pembayaran'  => 'required',
          'disc'              => 'nullable|numeric',
          'harga_id'          => 'required',
          'customer_id'       => 'required',
          // F1: antar-jemput (opsional — transaksi lama tidak mengirim ini)
          'ongkir'            => 'nullable|numeric|min:0',
          'jarak_km'          => 'nullable|numeric|min:0',
          'mode_layanan'      => 'nullable|in:reguler,pickup,dropoff',
          'alamat_jemput'     => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
      return [
        'status_payment.required'   => 'Status Pembayaran wajib dipilih.',
        'kg.required'               => 'Berat Pakaian tidak boleh kosong.',
        'kg.numeric'                => 'Berat Pakaian hanya mendukung angka.',
        'hari.required'             => 'Hari tidak boleh kosong.',
        'harga.required'            => 'Harga tidak boleh kosong.',
        'jenis_pembayaran.required' => 'Jenis Pembayaran wajib dipilih.',
        'disc.numeric'              => 'Diskon hanya mendukung angka.',
        'harga_id.required'         => 'Jenis Pakaian wajib dipilih.',
        'customer_id.required'      => 'Customer wajib dipilih.',
        'ongkir.numeric'            => 'Ongkir harus berupa angka.',
        'ongkir.min'                => 'Ongkir tidak boleh negatif.',
        'jarak_km.numeric'          => 'Jarak harus berupa angka.',
        'mode_layanan.in'           => 'Mode layanan tidak dikenal.'
      ];
    }
}
