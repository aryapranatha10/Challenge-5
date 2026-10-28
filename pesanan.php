<?php
declare(strict_types=1);
require __DIR__ . '/includes/data.php';

$galat = [];
$input = ['nama' => '', 'hp' => '', 'alamat' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        http_response_code(400);
        exit('Permintaan tidak valid. Muat ulang halaman lalu coba lagi.');
    }

    $aksi = (string) ($_POST['aksi'] ?? '');
    $id   = (int) ($_POST['id'] ?? 0);

    switch ($aksi) {
        case 'tambah':
            keranjang_tambah($id);
            flash_set('Produk ditambahkan ke pesanan.');
            $kat = (string) ($_POST['kat'] ?? '');
            alihkan(in_array($kat, kategori_semua(), true) ? 'index.php?kat=' . urlencode($kat) : 'index.php');
            break;

        case 'ubah':
            keranjang_ubah($id, (int) ($_POST['qty'] ?? 1));
            alihkan('pesanan.php');
            break;

        case 'hapus':
            keranjang_hapus($id);
            alihkan('pesanan.php');
            break;

        case 'kosongkan':
            keranjang_kosongkan();
            flash_set('Pesanan dikosongkan.');
            alihkan('pesanan.php');
            break;

        case 'kirim':
            $input = [
                'nama'   => trim((string) ($_POST['nama'] ?? '')),
                'hp'     => trim((string) ($_POST['hp'] ?? '')),
                'alamat' => trim((string) ($_POST['alamat'] ?? '')),
            ];
            if (keranjang_jumlah() === 0) {
                $galat[] = 'Pesanan masih kosong. Pilih produk dulu.';
            }
            if (mb_strlen($input['nama']) < 3) {
                $galat[] = 'Nama minimal 3 karakter.';
            }
            if (!preg_match('/^[0-9+\- ]{8,16}$/', $input['hp'])) {
                $galat[] = 'Nomor HP harus 8–16 karakter berisi angka, spasi, + atau -.';
            }
            if (mb_strlen($input['alamat']) < 10) {
                $galat[] = 'Alamat minimal 10 karakter.';
            }

            if (!$galat) {
                $baris = keranjang_baris();
                $total = keranjang_total();
                $kode  = 'RG-' . date('ymd') . '-' . random_int(1000, 9999);

                $teks = 'Halo ' . NAMA_TOKO . ', saya ingin konfirmasi pesanan ' . $kode . ":\n";
                foreach ($baris as $b) {
                    $teks .= '- ' . $b['nama'] . ' x' . $b['qty'] . "\n";
                }
                $teks .= 'Total: ' . rupiah($total) . "\nNama: " . $input['nama'] . "\nAlamat: " . $input['alamat'];

                $_SESSION['pesanan_sukses'] = [
                    'kode'  => $kode,
                    'nama'  => $input['nama'],
                    'total' => $total,
                    'wa'    => 'https://wa.me/' . NOMOR_WA . '?text=' . rawurlencode($teks),
                ];
                keranjang_kosongkan();
                alihkan('pesanan.php');
            }
            break;
    }
}

$sukses = $_SESSION['pesanan_sukses'] ?? null;
unset($_SESSION['pesanan_sukses']);

$baris = keranjang_baris();
$judul = 'Pesanan';
require __DIR__ . '/includes/header.php';
?>
<h1>Pesanan Anda</h1>

<?php if ($sukses !== null): ?>
    <div class="notice" role="status">
        <h2>Pesanan <?= e($sukses['kode']) ?> diterima</h2>
        <p>Terima kasih, <?= e($sukses['nama']) ?>. Total pesanan <?= e(rupiah($sukses['total'])) ?>. Kirim rincian ke WhatsApp toko untuk konfirmasi dan pembayaran.</p>
        <a class="btn" href="<?= e($sukses['wa']) ?>" target="_blank" rel="noopener">Konfirmasi lewat WhatsApp</a>
    </div>
<?php endif; ?>

<?php if ($galat): ?>
    <div class="notice error" role="alert">
        <strong>Pesanan belum bisa dikirim:</strong>
        <ul>
            <?php foreach ($galat as $g): ?><li><?= e($g) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!$baris): ?>
    <?php if ($sukses === null): ?>
        <p class="muted">Pesanan masih kosong. <a href="index.php">Pilih perlengkapan</a> untuk memulai.</p>
    <?php endif; ?>
<?php else: ?>
    <table>
        <thead><tr><th>Produk</th><th class="num">Harga</th><th>Jumlah</th><th class="num">Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($baris as $b): ?>
            <tr>
                <td><?= e($b['nama']) ?></td>
                <td class="num"><?= e(rupiah($b['harga'])) ?></td>
                <td>
                    <form method="post" action="pesanan.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="ubah">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <input type="number" name="qty" min="0" max="99" value="<?= $b['qty'] ?>" aria-label="Jumlah <?= e($b['nama']) ?>">
                        <button class="btn alt" type="submit">Ubah</button>
                    </form>
                </td>
                <td class="num"><?= e(rupiah($b['subtotal'])) ?></td>
                <td>
                    <form method="post" action="pesanan.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <button class="btn link" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3">Total</th><th class="num"><?= e(rupiah(keranjang_total())) ?></th><th></th></tr></tfoot>
    </table>

    <form method="post" action="pesanan.php" style="margin-top:12px">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="kosongkan">
        <button class="btn link" type="submit">Kosongkan pesanan</button>
    </form>

    <h2 style="margin-top:32px">Data pemesan</h2>
    <form method="post" action="pesanan.php" style="max-width:520px">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="kirim">
        <label for="nama">Nama lengkap</label>
        <input id="nama" name="nama" required minlength="3" value="<?= e($input['nama']) ?>">
        <label for="hp">Nomor HP</label>
        <input id="hp" name="hp" type="tel" required value="<?= e($input['hp']) ?>">
        <label for="alamat">Alamat pengiriman</label>
        <textarea id="alamat" name="alamat" rows="3" required minlength="10"><?= e($input['alamat']) ?></textarea>
        <p><button class="btn" type="submit">Kirim pesanan</button></p>
    </form>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
