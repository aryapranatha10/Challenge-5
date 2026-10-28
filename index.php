<?php
declare(strict_types=1);
require __DIR__ . '/includes/data.php';

$kategori = kategori_semua();
$kat      = (string) ($_GET['kat'] ?? '');
if (!in_array($kat, $kategori, true)) {
    $kat = ''; // tidak dikenal = tampilkan semua
}

$produk = array_filter(
    produk_semua(),
    fn (array $p): bool => $kat === '' || $p['kategori'] === $kat
);

$judul = 'Perlengkapan Pendakian';
require __DIR__ . '/includes/header.php';
?>
<h1>Perlengkapan untuk jalur yang belum Anda kenal.</h1>
<p class="muted">Tenda, carrier, sepatu, dan jaket yang sudah kami uji di jalur Rinjani, Semeru, dan Kerinci.</p>

<nav class="filters" aria-label="Filter kategori">
    <a href="index.php"<?= $kat === '' ? ' aria-current="true"' : '' ?>>Semua</a>
    <?php foreach ($kategori as $k): ?>
        <a href="index.php?kat=<?= urlencode($k) ?>"<?= $kat === $k ? ' aria-current="true"' : '' ?>><?= e($k) ?></a>
    <?php endforeach; ?>
</nav>

<div class="grid" id="produk">
    <?php foreach ($produk as $id => $p): ?>
        <article class="card">
            <span class="cat"><?= e($p['kategori']) ?></span>
            <h3><?= e($p['nama']) ?></h3>
            <span class="spek"><?= e($p['spek']) ?></span>
            <div class="row">
                <strong><?= e(rupiah($p['harga'])) ?></strong>
                <form method="post" action="pesanan.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="aksi" value="tambah">
                    <input type="hidden" name="id" value="<?= (int) $id ?>">
                    <input type="hidden" name="kat" value="<?= e($kat) ?>">
                    <button class="btn alt" type="submit">Tambah</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
