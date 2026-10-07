<?php
$base='/home/agent/projects/laundry-saas';
require $base.'/vendor/autoload.php';
$app = require_once $base.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/login','GET'));
$user = App\Models\User::where('email','admin.tbn@laundry.com')->first();
Illuminate\Support\Facades\Auth::login($user);
foreach (['read-notifikasi','admin','update-satatus-karyawan','finance','settings','edit-harga','profile-admin-edit','customer/create','transaksi/create'] as $uri) {
    try {
        $req=Illuminate\Http\Request::create('/'.$uri,'GET');
        $req->setLaravelSession(app('session.store'));
        $resp=$kernel->handle($req);
        $body=$resp->getContent();
        // ambil judul exception
        $t='';
        if (preg_match('/<title>(.*?)<\/title>/s',$body,$m)) $t=trim(strip_tags(html_entity_decode($m[1])));
        $msg='';
        if (preg_match('/(BadMethodCallException|View \[[^\]]+\] not found|Class "[^"]+" not found|SQLSTATE\[[^\]]+\]|Call to undefined [^<]{0,80})/',$body,$m)) $msg=$m[1];
        echo "--- /$uri => {$resp->getStatusCode()}\n    TITLE: ".substr($t,0,150)."\n    MSG: ".substr($msg,0,200)."\n";
    } catch(\Throwable $e){ echo "--- /$uri => EXC ".get_class($e)." :: ".substr($e->getMessage(),0,150)."\n"; }
}
