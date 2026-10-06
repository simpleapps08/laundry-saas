<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{Notification, transaksi,User};

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::check()){
          if (Auth::user()->auth === "Admin") {
              $masuk = transaksi::whereIN('status_order',['Process','Done','Delivery'])->count();
              $selesai = transaksi::where('status_order','Done')->count();
              $diambil = transaksi::where('status_order','Delivery')->count();
              $customer = User::where('auth','Customer')->get();
              $sudahbayar = transaksi::where('status_payment','Success')->count();
              $belumbayar = transaksi::where('status_payment','Pending')->count();
              $incomeY = transaksi::where('status_payment','Success')
              ->where('tahun',date('Y'))->sum('harga_akhir');

              $incomeM = transaksi::where('status_payment','Success')
              ->where('tahun',date('Y'))->where('bulan', ltrim(date('m'),'0'))->sum('harga_akhir');

              $incomeYOld = transaksi::where('status_payment','Success')
              ->where('tahun',date("Y",strtotime("-1 month")))->sum('harga_akhir');

              $incomeD = transaksi::where('status_payment','Success')
              ->where('tahun',date('Y'))->where('bulan', ltrim(date('m'),'0'))->where('tgl',ltrim(date('d'),'0'))->sum('harga_akhir');

              $incomeDOld = transaksi::where('status_payment','Success')->where('tahun',date('Y'))
              ->where('bulan', ltrim(date('m'),'0'))->where('tgl',ltrim(date("d",strtotime("-1 day")),'0'))->sum('harga_akhir');

              $data = DB::table("transaksis")
                  ->select("id" ,DB::raw("(COUNT(*)) as customer"))
                  ->orderBy('created_at')
                  ->groupBy(DB::raw("MONTH(created_at)"))
                  ->count();

              // Statistik Harian
              $hari = DB::table('transaksis')
              ->  select('tgl', DB::raw('count(id) AS jml'))
              ->  whereYear('created_at','=',date("Y", strtotime(now())))
              ->  whereMonth('created_at','=',date("m", strtotime(now())))
              ->  groupBy('tgl')
              ->  get();

              $tanggal = '';
              $batas =  31;
              $nilai = '';
              for($_i=1; $_i <= $batas; $_i++){
                  $tanggal = $tanggal . (string)$_i . ',';
                  $_check = false;
                  foreach($hari as $_data){
                      if((int)@$_data->tgl === $_i){
                          $nilai = $nilai . (string)$_data->jml . ',';
                          $_check = true;
                      }
                  }
                  if(!$_check){
                      $nilai = $nilai . '0,';
                  }
              }

              // Statistik Bulanan
              // FASE 2B: pakai kolom date `tanggal_masuk`, bukan `bulan` (string)
              $bln = DB::table('transaksis')
              ->  select(DB::raw('MONTH(tanggal_masuk) AS bulan'), DB::raw('count(id) AS jml'))
              ->  whereYear('tanggal_masuk','=',date("Y", strtotime(now())))
              ->  whereMonth('tanggal_masuk','=',date("m", strtotime(now())))
              ->  groupBy(DB::raw('MONTH(tanggal_masuk)'))
              ->  get();

              $bulans = '';
              $batas =  12;
              $nilaiB = '';
              for($_i=1; $_i <= $batas; $_i++){
                  $bulans = $bulans . (string)$_i . ',';
                  $_check = false;
                  foreach($bln as $_data){
                      if((int)@$_data->bulan === $_i){
                          $nilaiB = $nilaiB . (string)$_data->jml . ',';
                          $_check = true;
                      }
                  }
                  if(!$_check){
                      $nilaiB = $nilaiB . '0,';
                  }
              }

              return view('modul_admin.index')
                  ->  with('data', $data)
                  ->  with('masuk',$masuk)
                  ->  with('selesai',$selesai)
                  ->  with('customer', $customer)
                  ->  with('sudahbayar', $sudahbayar)
                  ->  with('belumbayar', $belumbayar)
                  ->  with('_tanggal', substr($tanggal, 0,-1))
                  ->  with('_nilai', substr($nilai, 0, -1))
                  ->  with('_bulan', substr($bulans, 0,-1))
                  ->  with('_nilaiB', substr($nilaiB, 0, -1))
                  ->  with('diambil',$diambil)
                  ->  with('incomeY',$incomeY)
                  ->  with('incomeM',$incomeM)
                  ->  with('incomeYOld',$incomeYOld)
                  ->  with('incomeD',$incomeD)
                  ->  with('incomeDOld',$incomeDOld);

          } elseif(Auth::user()->auth === "Karyawan") {
              // FASE 2A: scope MilikCabang otomatis filter per cabang_id
              $masuk = transaksi::whereIN('status_order',['Process','Done','Delivery'])->count();
              $selesai = transaksi::where('status_order','Done')->count();
              $diambil = transaksi::where('status_order','Delivery')->count();
              $customer = User::where('karyawan_id',auth::user()->id)->get();

              // FASE 2B: SUM() pakai kolom *_numeric (kolom lama bertipe string).
              // Filter tanggal pakai `tanggal_masuk` (date), bukan tgl/bulan/tahun (string).
              $kgToday = transaksi::whereDate('tanggal_masuk', date('Y-m-d'))
              ->sum('kg_numeric');

              $kgTodayOld = transaksi::whereDate('tanggal_masuk', date('Y-m-d', strtotime('-1 day')))
              ->sum('kg_numeric');

              $incomeM = transaksi::where('status_payment','Success')
              ->whereYear('tanggal_masuk', date('Y'))
              ->whereMonth('tanggal_masuk', date('m'))
              ->sum('harga_akhir_numeric');

              $incomeMOld = transaksi::where('status_payment','Success')
              ->whereYear('tanggal_masuk', date('Y'))
              ->whereMonth('tanggal_masuk', date('m', strtotime('-1 month')))
              ->sum('harga_akhir_numeric');

              $persen = 0;
              if ($incomeMOld != null && $incomeM != null) {
                $persen =  ($incomeM - $incomeMOld) / $incomeM * 100;
              }

              // Statistik Bulanan
              // FASE 2B: pakai kolom date `tanggal_masuk`, bukan `bulan` (string)
              $bln = DB::table('transaksis')
              ->  select(DB::raw('MONTH(tanggal_masuk) AS bulan'), DB::raw('count(id) AS jml'))
              ->  whereYear('tanggal_masuk','=',date("Y", strtotime(now())))
              ->  whereMonth('tanggal_masuk','=',date("m", strtotime(now())))
              ->  groupBy(DB::raw('MONTH(tanggal_masuk)'))
              ->  get();

              $bulans = '';
              $batas =  12;
              $nilaiB = '';
              for($_i=1; $_i <= $batas; $_i++){
                  $bulans = $bulans . (string)$_i . ',';
                  $_check = false;
                  foreach($bln as $_data){
                      if((int)@$_data->bulan === $_i){
                          $nilaiB = $nilaiB . (string)$_data->jml . ',';
                          $_check = true;
                      }
                  }
                  if(!$_check){
                      $nilaiB = $nilaiB . '0,';
                  }
              }

              return view('karyawan.index')
                  ->  with('diambil', $diambil)
                  ->  with('masuk',$masuk)
                  ->  with('selesai',$selesai)
                  ->  with('customer', $customer)
                  ->  with('kgToday', $kgToday)
                  ->  with('kgTodayOld', $kgTodayOld)
                  ->  with('incomeM',$incomeM)
                  ->  with('incomeMOld',$incomeMOld)
                  ->  with('persen',$persen)
                  ->  with('_bulan', substr($bulans, 0,-1))
                  ->  with('_nilaiB', substr($nilaiB, 0, -1));

          }elseif(Auth::user()->auth == 'Customer'){
            $totalLaundry = transaksi::where('customer_id',Auth::id())->count();
            // FASE 2B: sum pakai kolom numerik
            $totalLaundryKg = transaksi::where('customer_id',Auth::id())->sum('kg_numeric');

            $transaksi = transaksi::with('price')->where('customer_id',Auth::id())->get();
            return view('customer.index',\compact('totalLaundry','totalLaundryKg','transaksi'));
          }
        }
    }

    // Read Notifikasi
    public function readNotifikasi(Request $request)
    {
        $notif = Notification::find($request->id);
        $notif->update([
            'is_read'   => 1
        ]);

        return $notif;
    }

}
