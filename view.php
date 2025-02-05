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
$url = "https://gubuk.my.id/v/" . $video_id;
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

// Mengambil konten dalam <div class="content-container mb-5">, khusus bagian <p>
$containerNodes = $xpath->query('//div[@class="content-container mb-5"]//p');
$containerHTML = '';
$size = 'Size tidak ditemukan';
$duration = 'Durasi tidak ditemukan';
$date = 'Tanggal tidak ditemukan';

foreach ($containerNodes as $node) {
    $text = trim($node->nodeValue);
    
    if (stripos($text, 'Size') !== false) {
        $size = $text;
    } elseif (stripos($text, 'Durasi') !== false) {
        $duration = $text;
    } elseif (stripos($text, 'Tanggal') !== false) {
        $date = $text;
    } else {
        $containerHTML .= $dom->saveHTML($node);
    }
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

<!-- Menyertakan CSS langsung di dalam file PHP -->
<style>
/* Mengubah warna teks default menjadi oranye */
body {
    color: #FFA500; /* Teks berwarna oranye */
}

h1, h2, h3, h4, h5, h6 {
    color: #FFA500; /* Judul juga menjadi oranye */
}

p {
    color: #FFA500; /* Paragraf berwarna oranye */
}

a {
    color: #FFA500; /* Link berwarna oranye */
}

.btn-warning:hover, button[type="submit"]:hover {
    background-color: #FF8C00; /* Warna tombol saat hover */
    border-color: #FF8C00; /* Border tombol saat hover */
}

/* Gaya untuk input pencarian */
input[type="text"] {
    color: #FFA500; /* Warna teks input pencarian */
    background-color: #333; /* Latar belakang input pencarian */
    border: 1px solid #FFA500; /* Border input pencarian */
}

button[type="submit"] {
    background-color: #FFA500; /* Warna tombol pencarian */
    border: 1px solid #FFA500; /* Border tombol pencarian */
    color: #fff; /* Teks tombol pencarian berwarna putih */
}

button[type="submit"]:hover {
    background-color: #FF8C00; /* Warna tombol saat hover */
}

/* CSS untuk .data-card */
.data-card {
    border: 1px solid #444;
    border-radius: 8px;
    padding: 10px;
    background-color: #1c1c1c;
    text-align: center;
}

.data-card .title {
    color: #FFA500; /* Teks berwarna oranye */
}

.content-container p {
    color: #FFA500; /* Teks konten utama berwarna oranye */
}

/* Style untuk kontainer video */
.responsive-container iframe {
    border: none;
    border-radius: 8px;
    width: 100%;
    height: 500px;
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

/* Menambahkan warna oranye pada border .data-card */
.data-card {
    border: 1px solid #FFA500; /* Border kartu berwarna oranye */
    border-radius: 8px;         /* Menambahkan sudut melengkung pada border */
    padding: 10px;              /* Memberikan padding di dalam kartu */
    background-color: #1c1c1c;  /* Latar belakang kartu yang gelap */
    text-align: center;         /* Memusatkan teks di dalam kartu */
}

/* Mengubah teks dalam .data-card menjadi warna oranye */
.data-card h5.text-light {
    color: #FFA500; /* Mengubah warna teks h5 menjadi oranye */
}

.data-card p.text-light {
    color: #FFA500; /* Mengubah warna teks p menjadi oranye */
}

/* Mengubah warna tombol dalam .data-card menjadi oranye */
.data-card .btn {
    background-color: #FFA500; /* Menetapkan warna latar belakang tombol */
    border-color: #FFA500;     /* Menetapkan warna border tombol */
    color: white;              /* Menetapkan warna teks tombol menjadi putih */
}

.data-card .btn:hover {
    background-color: #FF8C00; /* Warna tombol saat hover */
    border-color: #FF8C00;     /* Warna border tombol saat hover */
}
/* Style untuk informasi video */
.video-info {
    background-color: #1c1c1c;
    border: 1px solid #FFA500;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

</style>

<div class="container my-5">
    <!-- Form Pencarian -->
    <div class="row mb-4">
        <div class="col-12">
            <form action="/" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari Video..." value="<?= htmlspecialchars($search_query) ?>">
                    <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i> Cari</button>
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

    <!-- Tombol Download -->
    <div class="text-center mb-4">
        <?php if ($iframeSrc): ?>
        <a href="/" class="btn btn-warning">
                <i class="fas fa-home"></i> Kembali
            </a>
            <a href="https://s.shopee.co.id/3fmAM8YTI6" class="btn btn-warning" download>
                <i class="fas fa-download"></i> Download Video
            </a>
        <?php endif; ?>
    </div>

    <!-- Informasi Video (Size, Durasi, Tanggal) -->
    <div class="video-info">
        <p><strong><?= htmlspecialchars($size) ?></strong></p>
        <?= $containerHTML ?>
    </div>

    <!-- Bagian Elemen Tambahan -->
    <h2 class="text-center mt-5">Video Lainnya</h2>
    <div class="row">
        <?= $itemsHTML ?>
    </div>
</div>

<?php include 'foot.php'; // Memuat foot.php ?>
