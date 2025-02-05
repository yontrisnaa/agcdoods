<?php
include 'head.php'; // Memuat head.php

// Fungsi untuk mengambil konten HTML menggunakan cURL
function get_html_content($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

// Menangkap parameter pencarian dan halaman
$search_query = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$base_url = "https://gubuk.my.id/cari.php?search=";

// URL untuk halaman pencarian
$search_url = $base_url . urlencode($search_query) . "&page=" . $page;
$html = get_html_content($search_url);

if (!$html) {
    echo "<div class='alert alert-danger'>Gagal mengambil data dari URL.</div>";
    exit;
}

// Memproses HTML dengan DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($dom);

// Mengambil data dari elemen dengan class 'card text-center p-3 bg-dark text-white'
$videoNodes = $xpath->query('//div[contains(@class, "card") and contains(@class, "text-center") and contains(@class, "bg-dark")]');

$videoHTML = '';
foreach ($videoNodes as $node) {
    // Mengambil elemen gambar
    $imgNode = $xpath->query('.//img', $node)->item(0);
    $imgSrc = $imgNode ? $imgNode->getAttribute('src') : '';

    // Mengambil judul
    $titleNode = $xpath->query('.//h5', $node)->item(0);
    $title = $titleNode ? trim($titleNode->textContent) : '';

    // Mengambil ukuran
    $sizeNode = $xpath->query('.//p[contains(text(), "Size:")]', $node)->item(0);
    $size = $sizeNode ? trim(str_replace('Size:', '', $sizeNode->textContent)) : '';

    // Mengambil durasi
    $durationNode = $xpath->query('.//p[contains(text(), "Duration:")]', $node)->item(0);
    $duration = $durationNode ? trim(str_replace('Duration:', '', $durationNode->textContent)) : '';

    // Mengambil tanggal
    $dateNode = $xpath->query('.//p[contains(text(), "Date:")]', $node)->item(0);
    $date = $dateNode ? trim(str_replace('Date:', '', $dateNode->textContent)) : '';

    // Mengambil URL 'View'
    $viewNode = $xpath->query('.//a[contains(@class, "btn")]', $node)->item(0);
    $viewUrl = $viewNode ? $viewNode->getAttribute('href') : '';

    // Membuat elemen video dengan data yang sudah diambil
    $videoHTML .= '<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">';
    $videoHTML .= '<div class="card bg-dark text-warning border-warning">';
    $videoHTML .= '<img src="' . $imgSrc . '" alt="icon" class="card-img-top img-fluid">';
    $videoHTML .= '<div class="card-body">';
    $videoHTML .= '<h5 class="card-title">' . htmlspecialchars($title) . '</h5>';
    $videoHTML .= '<p class="card-text">Size: ' . $size . '</p>';
    $videoHTML .= '<p class="card-text">Duration: ' . $duration . '</p>';
    $videoHTML .= '<p class="card-text">Date: ' . $date . '</p>';
    $videoHTML .= '<a href="' . $viewUrl . '" class="btn btn-warning">View</a>';
    $videoHTML .= '</div>';
    $videoHTML .= '</div>';
    $videoHTML .= '</div>';
}

// Mengambil data pagination
$paginationNodes = $xpath->query('//ul[@class="pagination justify-content-center"]');
$paginationHTML = '';
foreach ($paginationNodes as $node) {
    $paginationHTML .= $dom->saveHTML($node);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoodStream</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000; /* Tema hitam */
            color: #FFA500; /* Teks berwarna oranye */
        }
        .card {
    border: 1px solid #FFA500; /* Border kartu berwarna oranye */
}
.card h5 {
    white-space: nowrap;       /* Menghindari teks untuk dibungkus */
    overflow: hidden;          /* Menyembunyikan teks yang melebihi batas */
    text-overflow: ellipsis;   /* Menampilkan "..." jika teks terlalu panjang */
    display: block;            /* Memastikan elemen menjadi block-level */
    height: 3em;               /* Batasi tinggi elemen agar teks tidak meluber */
    line-height: 1.5em;        /* Mengatur tinggi baris agar teks lebih rapi */
}

/* CSS Grid untuk mobile */
@media (max-width: 767px) {
    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .col-12 {
        width: 50%; /* Membuat dua kolom pada tampilan mobile */
        padding: 10px;
    }
}
        .btn-warning {
            background-color: #FFA500; /* Tombol berwarna oranye */
            border-color: #FFA500; /* Border tombol berwarna oranye */
        }
        .btn-warning:hover {
            background-color: #FF8C00; /* Warna tombol saat hover */
            border-color: #FF8C00; /* Border tombol saat hover */
        }
        .search-box {
            margin-bottom: 20px;
        }
                        /* CSS untuk memotong teks */
        .card h5 {
            white-space: nowrap;       /* Menghindari teks untuk dibungkus */
            overflow: hidden;          /* Menyembunyikan teks yang melebihi batas */
            text-overflow: ellipsis;   /* Menampilkan "..." jika teks terlalu panjang */
            display: block;            /* Memastikan elemen menjadi block-level */
            height: 3em;               /* Batasi tinggi elemen agar teks tidak meluber */
            line-height: 1.5em;        /* Mengatur tinggi baris agar teks lebih rapi */
        }

    </style>
</head>
<body>
<div class="container my-5">
    <!-- Kolom Pencarian -->
    <form class="search-box" method="GET" action="">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Cari Video..." value="<?= $search_query ?>">
            <button class="btn btn-warning" type="submit">Cari</button>
        </div>
    </form>

    <h2 class="text-center mb-4">Daftar Video</h2>
    <div class="row">
        <?= $videoHTML ?>
    </div>
    <div class="mt-4">
        <?= $paginationHTML ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include 'foot.php'; // Memuat foot.php
?>
