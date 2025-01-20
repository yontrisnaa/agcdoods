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

// Menangkap parameter video ID
$video_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
if (empty($video_id)) {
    echo "<div class='alert alert-danger'>Video ID tidak ditemukan.</div>";
    exit;
}

// URL untuk halaman video
$url = "https://doods.my.id/v/" . $video_id;
$html = get_html_content($url);

if (!$html) {
    echo "<div class='alert alert-danger'>Gagal mengambil data dari URL.</div>";
    exit;
}

// Memproses HTML dengan DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($dom);

// Mengambil data judul dan iframe
$titleNode = $xpath->query('//h1[@class="mb-4"]')->item(0);
$title = $titleNode ? $titleNode->nodeValue : 'Judul tidak ditemukan';

// Set title untuk halaman web
echo "<title>" . htmlspecialchars($title) . "</title>";

$iframeNode = $xpath->query('//iframe')->item(0);
$iframeSrc = $iframeNode ? $iframeNode->getAttribute('src') : '';

// Mengambil konten dalam <div class="container mt-3">, namun hanya bagian <p></p>
$containerNodes = $xpath->query('//div[@class="container mt-3"]//p');
$containerHTML = '';
foreach ($containerNodes as $node) {
    $containerHTML .= $dom->saveHTML($node);
}

// Mengambil elemen video lainnya
$itemNodes = $xpath->query('//div[@class="col-6 col-md-3 mb-4"]');
$itemsHTML = '';
foreach ($itemNodes as $item) {
    $itemsHTML .= $dom->saveHTML($item);
}
?>

<!-- Menyertakan CSS langsung di dalam file PHP -->
<style>
/* CSS untuk .data-card */
.data-card {
    border: 1px solid #444;
    border-radius: 8px;
    padding: 10px;
    background-color: #1c1c1c;
    text-align: center;
}

.data-card img {
    border-radius: 8px;
    width: 150px;
    height: 150px;
    object-fit: cover;
    margin: 0 auto;
    display: block;
}

.data-card .title {
    color: #fff;
    margin-top: 10px;
    font-size: 16px;
}

/* Style untuk kontainer video */
.responsive-container iframe {
    border: none;
    border-radius: 8px;
    width: 100%;
    height: 500px;
}

/* Style untuk konten video lainnya */
.row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}

.col-6.col-md-3.mb-4 {
    flex: 1 1 100%;
    max-width: 250px;
}

/* Menambahkan responsivitas pada kolom */
@media (min-width: 576px) {
    .col-6.col-md-3.mb-4 {
        flex: 1 1 48%;
    }
}

@media (min-width: 768px) {
    .col-6.col-md-3.mb-4 {
        flex: 1 1 23%;
    }
}

/* Gaya untuk kontainer utama */
.content-container p {
    color: #f8f9fa;
    font-size: 16px;
    line-height: 1.5;
}

/* Menambahkan margin pada elemen utama */
.container {
    padding-left: 15px;
    padding-right: 15px;
}

/* Gaya untuk tombol download */
.btn-download {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 30px; /* Memisahkan dengan konten atas */
    font-size: 16px;
    text-align: center;
}

.btn-download:hover {
    background-color: #0056b3;
}
</style>

<div class="container my-5">
        <!-- Form Pencarian -->
    <div class="row mb-4">
        <div class="col-12">
            <form action="/" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari Video..." value="<?= htmlspecialchars($search_query) ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </div> 
    <!-- Bagian Judul -->
    <h1 class="text-center mb-4"><?= htmlspecialchars($title) ?></h1>

    <!-- Bagian Iframe -->
    <div class="responsive-container mb-4">
        <?php if ($iframeSrc): ?>
            <iframe src="<?= htmlspecialchars($iframeSrc) ?>" allowfullscreen></iframe>
        <?php else: ?>
            <p class="text-danger">Video tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <!-- Tombol Download (Dipisah dari Konten Utama) -->
    <div class="text-center">
        <a href="https://www.profitablecpmrate.com/dbmtxn61?key=01d2f565d0d26f78c4da2f7d2c9e7339" class="btn-download" target="_blank" rel="noopener noreferrer">Download Video</a>
    </div>
    <br>

    <!-- Bagian Kontainer Utama -->
    <div class="content-container mb-5">
        <?= $containerHTML ?>
    </div>

    <!-- Bagian Elemen Tambahan -->
    <h2 class="text-center mt-5">Video Lainnya</h2>
    <div class="row">
        <?= $itemsHTML ?>
    </div>
</div>

<?php include 'foot.php'; // Memuat foot.php ?>
