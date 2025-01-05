<?php
include '/head.php'; // Memuat head.php

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
$base_url = "https://doods.my.id";

// URL untuk halaman pencarian
$search_url = $base_url . "?search=" . urlencode($search_query) . "&page=" . $page;
$html = get_html_content($search_url);

if (!$html) {
    echo "<div class='alert alert-danger'>Gagal mengambil data dari URL.</div>";
    exit;
}

// Memproses HTML dengan DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($dom);

// Mengambil data dari elemen dengan class 'data-card'
$videoNodes = $xpath->query('//div[@class="data-card"]');

// Membuat HTML untuk daftar video tanpa pengulangan card
$videoHTML = '';
foreach ($videoNodes as $node) {
    // Mengambil elemen gambar
    $imgNode = $xpath->query('.//img', $node)->item(0);
    $imgSrc = $imgNode ? $imgNode->getAttribute('src') : '';

    // Mengambil judul
    $titleNode = $xpath->query('.//h5[@class="text-light"]', $node)->item(0);
    $title = $titleNode ? $titleNode->textContent : '';

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
    $viewNode = $xpath->query('.//a[@class="btn-view"]', $node)->item(0);
    $viewUrl = $viewNode ? $viewNode->getAttribute('href') : '';

    // Membuat elemen video dengan data yang sudah diambil
    $videoHTML .= '<div class="col-6 col-sm-6 col-md-4 col-lg-3">';
    $videoHTML .= '<div class="data-card">';
    $videoHTML .= '<img src="' . $imgSrc . '" alt="icon" class="img-fluid">';
    $videoHTML .= '<h5 class="text-light">' . htmlspecialchars($title) . '</h5>';
    $videoHTML .= '<a href="' . $viewUrl . '" class="btn btn-primary">View</a>';
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DoodStream - Cari dan Tonton Video">
    <title>DoodStream</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #121212;
            color: #f1f1f1;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .navbar-brand img {
            width: 150px;
            height: auto;
        }
        .input-group input {
            border-radius: 20px 0 0 20px;
        }
        .input-group button {
            border-radius: 0 20px 20px 0;
        }
        .content-container {
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
        }
        .card {
            background-color: #1e1e1e;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .pagination a, .pagination span {
            color: #f1f1f1;
        }
        .pagination a:hover, .pagination span:hover {
            background-color: #007bff;
            color: #fff;
        }
        .video-container {
            margin-bottom: 20px;
        }
        iframe {
            border: none;
        }
        /* CSS untuk data-card */
        .data-card {
            background-color: #1f1f1f;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            padding: 15px;
            transition: all 0.3s ease-in-out;
        }
        .data-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
            transform: translateY(-5px);
        }
        .data-card img {
            width: 100%;
            border-radius: 8px;
        }
        .data-card .card-body {
            padding: 10px;
        }
        .data-card .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
        }
        .data-card .card-text {
            color: #ccc;
            font-size: 0.9rem;
        }

        /* CSS untuk memotong teks */
        .data-card h5 {
            white-space: nowrap;       /* Menghindari teks untuk dibungkus */
            overflow: hidden;          /* Menyembunyikan teks yang melebihi batas */
            text-overflow: ellipsis;   /* Menampilkan "..." jika teks terlalu panjang */
            display: block;            /* Memastikan elemen menjadi block-level */
            height: 3em;               /* Batasi tinggi elemen agar teks tidak meluber */
            line-height: 1.5em;        /* Mengatur tinggi baris agar teks lebih rapi */
        }

        /* Responsiveness */
        @media (max-width: 576px) {
            .navbar-brand img {
                width: 120px;
            }
            .input-group input {
                font-size: 14px;
            }
            .input-group button {
                font-size: 14px;
            }
        }

        .page-link {
            background-color: #333;
            color: #f1f1f1;
            border: 1px solid #444;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .page-link:focus {
            outline: none;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <!-- Form Pencarian -->
    <div class="row mb-4">
        <div class="col-12">
            <form action="index.php" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari Video..." value="<?= htmlspecialchars($search_query) ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Video -->
    <h2 class="text-center mb-4">Daftar Video</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?= $videoHTML ?>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <?= $paginationHTML ?>
    </div>
</div>

<?php include '/foot.php'; // Memuat footer ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
