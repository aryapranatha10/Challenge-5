<?php
declare(strict_types=1);

/*
 * Data toko + fungsi bantu. Di-include paling awal oleh setiap halaman,
 * sebelum ada output apa pun (session_start dan header() butuh itu).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const NAMA_TOKO = 'Rimba Gear';
const NOMOR_WA  = '6281200000000'; // format 628xxx, ganti dengan nomor toko

function produk_semua(): array
{
    return [
        1  => ['nama' => 'Tenda Dome 2 Orang',        'kategori' => 'Tenda',    'harga' => 850000,  'spek' => 'Double layer, 2,1 kg, tahan hujan 3.000 mm'],
        2  => ['nama' => 'Tenda Ultralight 1 Orang',  'kategori' => 'Tenda',    'harga' => 1250000, 'spek' => '1,3 kg, cocok untuk pendakian solo'],
        3  => ['nama' => 'Carrier 60 L',              'kategori' => 'Carrier',  'harga' => 780000,  'spek' => 'Punggung ventilasi, rain cover bawaan'],
        4  => ['nama' => 'Daypack 25 L',              'kategori' => 'Carrier',  'harga' => 320000,  'spek' => 'Untuk tektok, ada kantong hidrasi'],
        5  => ['nama' => 'Sepatu Gunung Waterproof',  'kategori' => 'Sepatu',   'harga' => 995000,  'spek' => 'Sol anti selip, tinggi ankle 15 cm'],
        6  => ['nama' => 'Sandal Gunung',             'kategori' => 'Sepatu',   'harga' => 275000,  'spek' => 'Untuk istirahat di camp dan menyeberang sungai'],
        7  => ['nama' => 'Jaket Windproof',           'kategori' => 'Pakaian',  'harga' => 540000,  'spek' => 'Ringan, bisa dilipat ke kantong sendiri'],
        8  => ['nama' => 'Sleeping Bag 5°C',          'kategori' => 'Tidur',    'harga' => 460000,  'spek' => 'Isian sintetis, 900 g, aman untuk puncak 3.000 m'],
        9  => ['nama' => 'Matras Tiup',               'kategori' => 'Tidur',    'harga' => 390000,  'spek' => 'Tebal 5 cm, bobot 480 g'],
        10 => ['nama' => 'Kompor Portable + Gas',     'kategori' => 'Masak',    'harga' => 215000,  'spek' => 'Api stabil, muat dalam nesting'],
        11 => ['nama' => 'Headlamp 350 Lumen',        'kategori' => 'Aksesori', 'harga' => 185000,  'spek' => 'Tahan percikan air, baterai 40 jam'],
        12 => ['nama' => 'Trekking Pole (sepasang)',  'kategori' => 'Aksesori', 'harga' => 260000,  'spek' => 'Aluminium, bisa dilipat 3 ruas'],
    ];
}

function produk_cari(int $id): ?array
{
    return produk_semua()[$id] ?? null;
}

function kategori_semua(): array
{
    return array_values(array_unique(array_column(produk_semua(), 'kategori')));
}

/* ---------- Format & keamanan ---------- */

function e(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function rupiah(int $n): string
{
    return 'Rp' . number_format($n, 0, ',', '.');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    return isset($_POST['csrf']) && hash_equals(csrf_token(), (string) $_POST['csrf']);
}

function alihkan(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash_set(string $pesan): void
{
    $_SESSION['flash'] = $pesan;
}

function flash_ambil(): ?string
{
    $pesan = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $pesan;
}

/* ---------- Keranjang (disimpan di session) ---------- */

function keranjang(): array
{
    return $_SESSION['keranjang'] ?? [];
}

function keranjang_tambah(int $id): void
{
    if (produk_cari($id) === null) {
        return;
    }
    $_SESSION['keranjang'][$id] = min((keranjang()[$id] ?? 0) + 1, 99);
}

function keranjang_ubah(int $id, int $qty): void
{
    if ($qty <= 0) {
        keranjang_hapus($id);
        return;
    }
    if (produk_cari($id) !== null) {
        $_SESSION['keranjang'][$id] = min($qty, 99);
    }
}

function keranjang_hapus(int $id): void
{
    unset($_SESSION['keranjang'][$id]);
}

function keranjang_kosongkan(): void
{
    $_SESSION['keranjang'] = [];
}

function keranjang_jumlah(): int
{
    return (int) array_sum(keranjang());
}

function keranjang_baris(): array
{
    $baris = [];
    foreach (keranjang() as $id => $qty) {
        $p = produk_cari((int) $id);
        if ($p === null) {
            continue;
        }
        $baris[] = [
            'id'       => (int) $id,
            'nama'     => $p['nama'],
            'harga'    => $p['harga'],
            'qty'      => (int) $qty,
            'subtotal' => $p['harga'] * (int) $qty,
        ];
    }
    return $baris;
}

function keranjang_total(): int
{
    return (int) array_sum(array_column(keranjang_baris(), 'subtotal'));
}
