<?php
// Definisikan kelas HuffmanNode untuk representasi simpul dalam pohon Huffman
class HuffmanNode {
    public $char;    // Karakter
    public $freq;    // Frekuensi kemunculan karakter
    public $left;    // Anak kiri dalam pohon Huffman
    public $right;   // Anak kanan dalam pohon Huffman

    // Konstruktor untuk inisialisasi simpul HuffmanNode
    public function __construct($char, $freq) {
        $this->char = $char;
        $this->freq = $freq;
        $this->left = null;
        $this->right = null;
    }
}

// Fungsi untuk membangun tabel frekuensi karakter dari data input
function buildFreqTable($data) {
    $freqTable = array_count_values(str_split($data)); // Menghitung frekuensi kemunculan setiap karakter
    arsort($freqTable); // Mengurutkan tabel frekuensi secara menurun berdasarkan frekuensi
    return $freqTable;
}

// Fungsi untuk membangun pohon Huffman berdasarkan tabel frekuensi karakter
function buildHuffmanTree($freqTable) {
    $heap = new SplPriorityQueue(); // Menggunakan priority queue untuk menyimpan simpul Huffman

    // Memasukkan setiap karakter sebagai simpul Huffman dengan frekuensinya ke dalam priority queue
    foreach ($freqTable as $char => $freq) {
        $heap->insert(new HuffmanNode($char, $freq), -$freq); // Nilai negatif agar diurutkan dari yang terbesar
    }

    // Menggabungkan simpul-simpul dalam priority queue menjadi pohon Huffman
    while ($heap->count() > 1) {
        $left = $heap->extract(); // Mengambil simpul dengan frekuensi tertinggi
        $right = $heap->extract(); // Mengambil simpul dengan frekuensi tertinggi kedua

        // Membuat simpul baru yang merupakan hasil penggabungan dua simpul sebelumnya
        $merged = new HuffmanNode(null, $left->freq + $right->freq);
        $merged->left = $left;
        $merged->right = $right;

        // Memasukkan simpul baru ke dalam priority queue
        $heap->insert($merged, -$merged->freq); // Nilai negatif agar diurutkan dari yang terbesar
    }

    // Mengembalikan simpul akar pohon Huffman
    return $heap->extract();
}

// Fungsi untuk membangun kode Huffman dari pohon Huffman
function buildHuffmanCodes($root) {
    $codes = array(); // Array untuk menyimpan kode Huffman dari setiap karakter
    buildCodesRecursive($root, '', $codes); // Memanggil fungsi rekursif untuk membangun kode Huffman
    return $codes;
}

// Fungsi rekursif untuk membangun kode Huffman dari pohon Huffman
function buildCodesRecursive($node, $code, &$codes) {
    // Jika simpul merupakan simpul daun (berisi karakter), simpan kode Huffman-nya
    if ($node->char !== null) {
        $codes[$node->char] = $code;
        return;
    }

    // Memanggil diri sendiri untuk simpul anak kiri dan anak kanan
    buildCodesRecursive($node->left, $code . '0', $codes); // Menambah '0' untuk anak kiri
    buildCodesRecursive($node->right, $code . '1', $codes); // Menambah '1' untuk anak kanan
}

// Fungsi untuk mengompres data menggunakan kode Huffman
function huffmanCompress($data) {
    $freqTable = buildFreqTable($data); // Membangun tabel frekuensi karakter dari data
    $huffmanTree = buildHuffmanTree($freqTable); // Membangun pohon Huffman dari tabel frekuensi
    $huffmanCodes = buildHuffmanCodes($huffmanTree); // Membangun kode Huffman dari pohon Huffman

    $encodedData = '';
    // Mengganti setiap karakter dalam data dengan kode Huffman-nya
    for ($i = 0; $i < strlen($data); $i++) {
        $encodedData .= $huffmanCodes[$data[$i]];
    }

    return $encodedData; // Mengembalikan data yang sudah terkompresi
}

// Fungsi untuk menulis data terkompresi ke dalam file biner
function writeCompressedData($encodedData, $outputFile) {
    // Memecah data terkompresi menjadi byte-byte berukuran 8 bit
    $encodedDataBytes = str_split($encodedData, 8);
    // Mengubah setiap byte biner menjadi nilai desimal dan menyimpannya dalam array
    $encodedBytes = array_map(function($byte) { return bindec($byte); }, $encodedDataBytes);

    // Membuka file untuk menulis data biner
    $fp = fopen($outputFile, 'wb');
    // Menulis setiap byte ke dalam file biner
    foreach ($encodedBytes as $byte) {
        fwrite($fp, pack('C', $byte));
    }
    fclose($fp); // Menutup file
}

// Fungsi utama untuk mengompresi gambar menggunakan metode Huffman
function compressImage($inputFile, $outputFile) {
    $data = file_get_contents($inputFile); // Membaca seluruh konten dari file gambar
    $encodedData = huffmanCompress($data); // Mengompresi data gambar menggunakan kode Huffman
    writeCompressedData($encodedData, $outputFile); // Menyimpan data terkompresi ke dalam file
}

// Proses kompresi jika ada file gambar yang diunggah melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image']['tmp_name'])) {
    header('Content-Type: application/json'); // Mengatur header untuk respon JSON

    $targetDir = "uploads/"; // Direktori untuk menyimpan file yang diunggah
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Membuat direktori jika belum ada
    }

    $targetFile = $targetDir . basename($_FILES["image"]["name"]); // Path lengkap file yang diunggah

    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    // Memeriksa apakah file yang diunggah merupakan file JPEG
    if ($imageFileType != "jpg" && $imageFileType != "jpeg") {
        echo json_encode(["status" => "error", "message" => "Hanya file JPEG yang diperbolehkan."]);
        exit;
    }

    // Memeriksa ukuran file yang diunggah (maksimal 500KB)
    if ($_FILES["image"]["size"] > 500000) {
        echo json_encode(["status" => "error", "message" => "Maaf, ukuran gambar terlalu besar."]);
        exit;
    }

    // Membuat direktori untuk menyimpan gambar terkompresi jika belum ada
    if (!is_dir("compressed_images")) {
        mkdir("compressed_images", 0777, true);
    }

    // Memindahkan file yang diunggah ke direktori tujuan
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $outputFile = "compressed_images/" . basename($_FILES["image"]["name"]);
        compressImage($targetFile, $outputFile); // Melakukan kompresi gambar
        echo json_encode(["status" => "success", "downloadLink" => $outputFile]); // Menyediakan tautan untuk mengunduh gambar terkompresi
    } else {
        echo json_encode(["status" => "error", "message" => "Maaf, terjadi kesalahan saat mengunggah gambar."]);
    }
}
?>
