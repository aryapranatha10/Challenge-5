<?php
declare(strict_types=1);
require __DIR__ . '/includes/data.php';

$judul = 'Tentang Kami';
require __DIR__ . '/includes/header.php';
?>
<h1>Tentang <?= e(NAMA_TOKO) ?></h1>
<p class="muted" style="max-width:60ch">Kami toko kecil yang dijalankan pendaki. Kami tahu apa yang dibutuhkan di ketinggian 3.000 meter, karena kami juga membawanya sendiri.</p>

<div class="cols">
    <div><h3>Diuji di jalur</h3><p>Setiap tenda dan carrier kami pakai dulu sebelum dijual atau disewakan.</p></div>
    <div><h3>Sewa per hari</h3><p>Belum yakin mau beli? Sewa tenda, sleeping bag, dan trekking pole mulai Rp15.000 per hari.</p></div>
    <div><h3>Cek perlengkapan gratis</h3><p>Kirim daftar bawaan Anda, kami periksa apa yang kurang sebelum berangkat.</p></div>
</div>

<h2 style="margin-top:40px">Kunjungi kami</h2>
<p>Jl. Raya Pendakian No. 12<br>Buka setiap hari, 09.00–20.00<br>WhatsApp: <?= e(NOMOR_WA) ?></p>
<p><a class="btn" href="index.php">Lihat perlengkapan</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
