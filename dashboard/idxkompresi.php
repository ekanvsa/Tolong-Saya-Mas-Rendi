<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kompresi Gambar</title>
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

        .form-label {
            color: #007bff;
            font-size: 20px;
        }

        .form-control {
            font-size: 16px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-size: 18px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1 class="mb-4">Kompresi Gambar</h1>
            <form id="imageForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="image" class="form-label">Pilih Gambar:</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <div id="imageError" class="text-danger"></div>
                </div>
                <button type="button" onclick="compressAndUpload()" class="btn btn-primary">Kompresi</button>
            </form>
        </div>
        <div class="mt-3 text-center">
            <a href="index.html" class="btn btn-secondary">Kembali</a>
            <a href="#" class="btn btn-primary" id="downloadLink" style="display: none;">Unduh</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/image-compressor.js/2.1.0/image-compressor.min.js"></script>
    <script>
        function compressAndUpload() {
            var fileInput = document.getElementById('image');
            var file = fileInput.files[0];
            var fileSize = file.size;

            var fileType = file.type;
            if (!fileType.match('image/jpeg')) {
                document.getElementById('imageError').innerHTML = 'Hanya file JPG yang diperbolehkan.';
                return;
            }

            if (fileSize > 5000000) { // Adjusted for larger JPG files if needed
                document.getElementById('imageError').innerHTML = 'Ukuran gambar terlalu besar.';
                return;
            }

            var options = {
                maxSizeMB: 1,
                maxWidthOrHeight: 800,
                useWebWorker: true,
                fileType: 'image/jpeg' // Set the output file type to JPEG
            };

            new ImageCompressor(file, {
                ...options,
                success(result) {
                    var formData = new FormData();
                    formData.append('image', result);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'compress_process.php', true);
                    xhr.onload = function() {
                        if (xhr.status == 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                var downloadLink = document.getElementById('downloadLink');
                                downloadLink.href = response.downloadLink;
                                downloadLink.style.display = 'block';
                            } else {
                                console.error('Error:', response);
                            }
                        } else {
                            console.error('Terjadi kesalahan saat mengunggah gambar.');
                        }
                    };
                    xhr.send(formData);
                },
                error(e) {
                    console.error(e.message);
                },
            });
        }
    </script>
</body>

</html>
