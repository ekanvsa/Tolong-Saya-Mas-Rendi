<?php
// Mengatur pengaturan output buffering dan kompresi
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
ini_set('implicit_flush', true);
ob_implicit_flush(true);

class MyClass {
    private $MAX_BIT_LENGTH;

    public function __construct($maxBitLength) {
        $this->MAX_BIT_LENGTH = $maxBitLength;
    }

    private function i2bin($length, $maxBitLength) {
        return str_pad(decbin($length), $maxBitLength, '0', STR_PAD_LEFT);
    }

    private function put_bits($bits) {
        // Implementasi sederhana untuk menampilkan bits
        echo "Binary representation: $bits\n";
    }

    public function processText($text) {
        $text_length = $this->i2bin(strlen($text), $this->MAX_BIT_LENGTH);
        $this->put_bits($text_length);
    }
}


// Fungsi untuk mengubah pesan menjadi biner
function messageToBinary($message) {
    $binaryMessage = '';
    $messageLength = strlen($message);

    // Menambahkan panjang pesan sebagai metadata (opsional)
    $binaryMessage .= str_pad(decbin($messageLength), 32, '0', STR_PAD_LEFT);

    for ($i = 0; $i < $messageLength; $i++) {
        // Mengubah setiap karakter dalam pesan menjadi representasi biner 8-bit
        $binaryMessage .= str_pad(decbin(ord($message[$i])), 8, '0', STR_PAD_LEFT);
    }

    // Menambahkan End of Message (EOM) untuk menandai akhir pesan
    $binaryMessage .= '11111111'; // Contoh penambahan EOM dengan 8 bit 1

    return $binaryMessage;
}


// Fungsi untuk menyisipkan pesan ke dalam gambar menggunakan teknik LSB
function embedLSB($imagePath, $message) {
    // Membuka gambar dari file JPEG
    $img = imagecreatefromjpeg($imagePath);
    // Mengubah pesan menjadi biner
    $binaryMessage = messageToBinary($message);
    // Menambahkan 16 angka 1 di sebelah kanan message yg sudah diubah ke biner <1111111111111111>
    // $binaryMessage .= str_pad('', 16, '1');

    // Mendapatkan panjang pesan biner
    $messageLength = strlen($binaryMessage);
    // Mendapatkan ukuran gambar (lebar dan tinggi)
    list($width, $height) = getimagesize($imagePath);
    // Menghitung jumlah maksimum piksel yang bisa digunakan untuk menyisipkan pesan
    $maxPixels = $width * $height * 3;

    // Mengecek apakah pesan terlalu panjang untuk disisipkan ke dalam gambar
    if ($messageLength > $maxPixels) {
        // Jika terlalu panjang, kirim pesan error dalam format JSON
        echo json_encode(["status" => "error", "message" => "Message too long to embed in the image"]);
        return;
    }

    $dataIndex = 0;
    $totalPixels = $width * $height;

    // Loop melalui setiap piksel dalam gambar
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            // Mendapatkan nilai RGB dari piksel saat ini
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Loop untuk setiap komponen warna (R, G, B)
            for ($i = 0; $i < 3; $i++) {
                if ($dataIndex < $messageLength) {
                    // Mendapatkan bit dari pesan biner sebelumnya
                    $bit = intval($binaryMessage[$dataIndex]);
                    // Menyisipkan bit ke dalam komponen warna yang sesuai
                    switch ($i) {
                        case 0:
                            $r = ($r & ~1) | $bit;
                            break;
                        case 1:
                            $g = ($g & ~1) | $bit;
                            break;
                        case 2:
                            $b = ($b & ~1) | $bit;
                            break;
                    }
                    $dataIndex++;
                }
            }

            // Menyimpan nilai RGB yang telah dimodifikasi kembali ke gambar
            $color = imagecolorallocate($img, $r, $g, $b);
            imagesetpixel($img, $x, $y, $color);
        }
    }

    // Menyimpan gambar yang telah dimodifikasi
    // printf('uploads/%s', $namaFile);
    $outputImagePath = 'uploads/embedded_image.jpg';
    imagejpeg($img, $outputImagePath);
    imagedestroy($img);

    // Mengirimkan respons sukses dalam format JSON
    echo json_encode(["status" => "success", "imagePath" => $outputImagePath]);
}

// Mengecek apakah form telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image']['tmp_name'])) {
    $tempName = $_FILES['image']['tmp_name'];
    $imagePath = 'uploads/' . basename($_FILES['image']['name']);
    // Memindahkan file gambar yang diupload ke direktori 'uploads'
    move_uploaded_file($tempName, $imagePath);

    // Menyisipkan pesan ke dalam gambar
    $message = $_POST['message'];
    embedLSB($imagePath, $message);
} else {
    // Jika permintaan tidak valid, kirim pesan error dalam format JSON
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
