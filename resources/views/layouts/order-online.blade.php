<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Javacom Laundry')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --tosca:#0d9488; --tosca-dark:#0e8175; --tosca-light:#ccfbf1;
            --ink:#0f172a; --grey:#64748b; --line:#e2e8f0; --bg:#f8fafc;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:'Inter',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--ink);line-height:1.55}
        .oo-top{background:#fff;border-bottom:1px solid var(--line);padding:14px 20px}
        .oo-brand{display:flex;align-items:center;gap:10px;max-width:720px;margin:0 auto;font-weight:700;color:var(--tosca-dark);text-decoration:none}
        .oo-brand .dot{width:26px;height:26px;border-radius:8px;background:var(--tosca);display:inline-block}
        .oo-wrap{max-width:720px;margin:0 auto;padding:24px 20px 60px}
        .oo-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px;box-shadow:0 1px 3px rgba(15,23,42,.04)}
        .oo-head{margin-bottom:20px}
        .oo-title{margin:0 0 6px;font-size:24px;font-weight:700}
        .oo-sub{margin:0;color:var(--grey);font-size:14px}
        .oo-form{margin-top:8px}
        .oo-field{margin-bottom:16px}
        .oo-field label{display:block;font-weight:600;font-size:14px;margin-bottom:6px}
        .oo-req{color:#dc2626}
        .oo-opt{color:var(--grey);font-weight:400;font-size:12px}
        .oo-field input[type=text],.oo-field input[type=tel],.oo-field input[type=number],
        .oo-field select,.oo-field textarea{
            width:100%;padding:11px 13px;border:1px solid var(--line);border-radius:10px;
            font-family:inherit;font-size:15px;background:#fff;color:var(--ink);
        }
        .oo-field input:focus,.oo-field select:focus,.oo-field textarea:focus{
            outline:none;border-color:var(--tosca);box-shadow:0 0 0 3px var(--tosca-light)
        }
        .oo-hint{display:block;margin-top:5px;color:var(--grey);font-size:12px}
        .oo-row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        @media(max-width:560px){.oo-row2{grid-template-columns:1fr}}
        .oo-radio-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        @media(max-width:560px){.oo-radio-row{grid-template-columns:1fr}}
        .oo-radio{display:block;cursor:pointer;border:2px solid var(--line);border-radius:12px;padding:12px;margin:0;transition:.15s}
        .oo-radio input{position:absolute;opacity:0}
        .oo-radio:has(input:checked){border-color:var(--tosca);background:var(--tosca-light)}
        .oo-radio-body b{display:block;font-size:14px}
        .oo-radio-body small{color:var(--grey);font-size:12px}
        .oo-btn{width:100%;padding:14px;background:var(--tosca-dark);color:#fff;border:none;border-radius:12px;
            font-size:16px;font-weight:600;cursor:pointer;font-family:inherit;margin-top:8px}
        .oo-btn:hover{background:#0b6b61}
        .oo-btn.secondary{background:#fff;color:var(--tosca-dark);border:2px solid var(--tosca-dark)}
        .oo-note{background:var(--tosca-light);border-radius:10px;padding:12px 14px;font-size:13px;color:#134e4a;margin-bottom:16px}
        .oo-alert{border-radius:10px;padding:12px 14px;font-size:14px;margin-bottom:16px}
        .oo-alert ul{margin:6px 0 0;padding-left:20px}
        .oo-alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
        .oo-alert-ok{background:var(--tosca-light);border:1px solid #99f6e4;color:#134e4a}
        .oo-foot{text-align:center;color:var(--grey);font-size:13px;margin-top:16px}
        .oo-foot a{color:var(--tosca-dark);font-weight:600}
        .oo-kode{font-size:26px;font-weight:700;letter-spacing:1px;color:var(--tosca-dark);
            background:#fff;border:2px dashed var(--tosca);border-radius:12px;padding:16px;text-align:center;margin:16px 0}
        .oo-steps{list-style:none;padding:0;margin:18px 0}
        .oo-steps li{position:relative;padding:0 0 16px 30px;font-size:14px;color:var(--grey)}
        .oo-steps li:before{content:"";position:absolute;left:6px;top:6px;width:10px;height:10px;border-radius:50%;background:var(--tosca)}
        .oo-steps li:after{content:"";position:absolute;left:10px;top:18px;bottom:0;width:2px;background:var(--line)}
        .oo-steps li:last-child:after{display:none}
        .oo-steps li b{color:var(--ink)}
        .oo-badge{display:inline-block;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;background:var(--tosca-light);color:#134e4a}
    </style>
</head>
<body>
    <header class="oo-top">
        <a href="{{ url('/') }}" class="oo-brand"><span class="dot"></span> Javacom Laundry</a>
    </header>
    @yield('content')
</body>
</html>
