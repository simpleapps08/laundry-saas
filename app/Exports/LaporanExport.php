<?php

namespace App\Exports;

use App\Models\transaksi;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
      // FASE 2A: ekspor per CABANG (sebelumnya per-akun, sehingga
      // laporan Excel tidak lengkap untuk karyawan dalam satu cabang).
      $data = transaksi::orderBy('id','DESC')->get();

      return view(
        'karyawan.laporan.excelExport',
        [
          'data'  => $data
        ]
      );
    }
}
