<?php
$base='/home/agent/projects/laundry-saas';
require $base.'/vendor/autoload.php';
$app = require_once $base.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::create('/login','GET'));
$email = $argv[1];
$user = App\Models\User::where('email',$email)->first();
echo "USER: $email | auth={$user->auth} | roles=".implode(',',$user->getRoleNames()->toArray())." | cabang_id={$user->cabang_id}\n";
Illuminate\Support\Facades\Auth::login($user);
foreach (app('router')->getRoutes() as $r) {
    if (!in_array('GET',$r->methods())) continue;
    $uri=$r->uri();
    if (str_starts_with($uri,'api')||str_contains($uri,'{')||$uri==='') continue;
    try {
        $req=Illuminate\Http\Request::create('/'.$uri,'GET');
        $req->setLaravelSession(app('session.store'));
        $resp=$kernel->handle($req);
        $code=$resp->getStatusCode();
        $body=substr($resp->getContent(),0,8000);
        $err='';
        if (preg_match('/(BadMethodCallException|View \[[^\]]+\] not found|Class "[^"]+" not found|SQLSTATE\[[^\]]+\])/',$body,$m)) $err=$m[1];
        printf('%-44s %s %s', '/'.$uri, $code, $err);
    } catch(\Throwable $e){ printf('%-44s EXC %s', '/'.$uri, substr($e->getMessage(),0,80)); }
    echo "\n";
}
