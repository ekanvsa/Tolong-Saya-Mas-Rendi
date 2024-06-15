<?php
if (isset($_GET['image'])) {
    $imagePath = urldecode($_GET['image']);
} else {
    die("No image path provided.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embed Gambar - Hasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background-color: #f4f4f4;
            min-height: 100vh;
        }
        .form-container {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin: 20px auto;
            max-width: 600px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container text-center">
            <h1 class="mb-4">Hasil Embed Gambar</h1>
            <p>Gambar dengan pesan rahasia telah berhasil di-embed.</p>
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Embedded Image" class="img-fluid mb-3">
            <a href="<?php echo htmlspecialchars($imagePath); ?>" download="embedded_image.jpg" class="btn btn-primary">Download Gambar</a>
            <div class="mt-3">
                <a href="index.html" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
