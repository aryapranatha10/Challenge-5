<?php
/* Pakai: set $judul sebelum include file ini. */
$judul   = $judul ?? NAMA_TOKO;
$halaman = basename($_SERVER['SCRIPT_NAME']);
$flash   = flash_ambil();
$menu    = ['index.php' => 'Produk', 'pesanan.php' => 'Pesanan', 'tentang.php' => 'Tentang'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> | <?= e(NAMA_TOKO) ?></title>
<style>
:root{--bg:#eef2f0;--surface:#fff;--ink:#17352b;--muted:#51655d;--line:#c9d4cf;--accent:#e8590c;--deep:#17352b;color-scheme:light dark}
@media (prefers-color-scheme:dark){:root{--bg:#0f1c17;--surface:#172a23;--ink:#e6eee9;--muted:#9bb0a7;--line:#2a4036;--deep:#0a1410}}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font:400 1rem/1.6 system-ui,-apple-system,"Segoe UI",sans-serif}
h1,h2,h3{line-height:1.15;margin:0 0 8px}
a{color:inherit}
:focus-visible{outline:3px solid var(--accent);outline-offset:2px}
.wrap{max-width:1040px;margin:0 auto;padding:0 20px}
.site-header{position:sticky;top:0;background:var(--bg);border-bottom:1px solid var(--line);z-index:5}
.bar{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:60px;flex-wrap:wrap}
.logo{font-weight:800;font-size:1.25rem;text-decoration:none}
.site-header nav a{margin-left:18px;text-decoration:none;color:var(--muted);font-weight:500}
.site-header nav a[aria-current="page"]{color:var(--ink);border-bottom:2px solid var(--accent)}
.badge{background:var(--accent);color:#fff;border-radius:999px;padding:0 8px;font-size:.8rem;margin-left:4px}
.btn{display:inline-block;background:var(--accent);color:#fff;border:0;border-radius:8px;padding:11px 20px;font:600 1rem inherit;font-family:inherit;text-decoration:none;cursor:pointer}
.btn.alt{background:none;color:var(--accent);border:1.5px solid var(--accent);padding:6px 14px;font-size:.9rem}
.btn.link{background:none;color:var(--muted);text-decoration:underline;padding:6px 8px;font-weight:400}
.hero{background:var(--deep);color:#eef2f0;padding:56px 0}
.hero h1{font-size:clamp(2rem,6vw,3.4rem);max-width:16ch}
.hero p{color:#c3d3cb;max-width:48ch;margin:12px 0 0}
main{padding:40px 0 64px}
.notice{background:var(--surface);border:1px solid var(--line);border-left:4px solid var(--accent);padding:12px 16px;border-radius:8px;margin-bottom:20px}
.error{border-left-color:#c92a2a}
.filters{display:flex;flex-wrap:wrap;gap:8px;margin:16px 0 24px}
.filters a{border:1.5px solid var(--line);background:var(--surface);padding:6px 16px;border-radius:999px;text-decoration:none}
.filters a[aria-current="true"]{background:var(--ink);color:var(--bg);border-color:var(--ink)}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:18px}
.card{background:var(--surface);border:1px solid var(--line);border-radius:10px;padding:18px;display:flex;flex-direction:column;gap:6px}
.card .cat{color:var(--muted);font-size:.85rem}
.card .spek{color:var(--muted);font-size:.92rem;flex:1}
.card .row{display:flex;justify-content:space-between;align-items:center;margin-top:10px}
table{width:100%;border-collapse:collapse;background:var(--surface);border:1px solid var(--line);border-radius:8px}
th,td{padding:10px 12px;text-align:left;border-bottom:1px solid var(--line);vertical-align:middle}
td.num,th.num{text-align:right}
input[type=number]{width:64px}
input,textarea{font:inherit;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;background:var(--surface);color:var(--ink);width:100%}
input[type=number]{width:70px}
label{display:block;font-weight:600;margin:14px 0 4px}
.cols{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:28px;margin-top:24px}
.muted{color:var(--muted)}
.site-footer{background:var(--deep);color:#c3d3cb;padding:32px 0}
.site-footer p{margin:2px 0}
@media (max-width:640px){.site-header nav a{margin:0 12px 0 0}}
</style>
</head>
<body>
<header class="site-header"><div class="wrap bar">
    <a class="logo" href="index.php"><?= e(NAMA_TOKO) ?></a>
    <nav aria-label="Navigasi utama">
        <?php foreach ($menu as $file => $label): ?>
            <a href="<?= e($file) ?>"<?= $halaman === $file ? ' aria-current="page"' : '' ?>><?= e($label) ?><?php if ($file === 'pesanan.php' && keranjang_jumlah() > 0): ?><span class="badge"><?= keranjang_jumlah() ?></span><?php endif; ?></a>
        <?php endforeach; ?>
    </nav>
</div></header>
<main><div class="wrap">
<?php if ($flash !== null): ?>
    <div class="notice" role="status"><?= e($flash) ?></div>
<?php endif; ?>
