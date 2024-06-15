<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Encode Gambar</title>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <h1 class="mb-4">Encode Gambar</h1>
      <form id="encodeForm" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="image" class="form-label">Pilih Gambar:</label>
          <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/jpg" required>
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Pesan Rahasia:</label>
          <input type="text" class="form-control" id="message" name="message" placeholder="Masukkan pesan rahasia" required>
        </div>
        <button type="submit" class="btn btn-primary">Encode</button>
      </form>
      <div id="progressContainer" class="mt-3" style="display: none;">
        <h3>Progress Embedding:</h3>
        <div class="progress">
          <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div id="progressStatus" class="mt-2"></div>
      </div>
    </div>
    <div class="mt-3 text-center">
      <a href="index.html" class="btn btn-secondary">Kembali</a>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $("#encodeForm").on("submit", function(event) {
        event.preventDefault();
        var formData = new FormData(this);//Mengambil file dari input file
        $("#progressContainer").show();//Animasi proges
        $("#progressStatus").text("Memulai proses embedding...");//Message proses

        $.ajax({
          url: "encode_process.php",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          xhr: function() {
            var xhr = new window.XMLHttpRequest();

            xhr.addEventListener("readystatechange", function() {
              if (xhr.readyState == 3) { // Loading state (partial data)
                var response = xhr.responseText.trim();
                var responses = response.split('\n');
                var lastResponse = responses[responses.length - 1];

                try {
                  var jsonResponse = JSON.parse(lastResponse);
                  if (jsonResponse.status === "step") {
                    var detail = jsonResponse.detail;
                    $("#progressBar").width((detail.step / formData.get('message').length * 100) + "%");//Menggabungkan file gambar dengan message
                    $("#progressBar").attr("aria-valuenow", detail.step);
                    $("#progressStatus").html //Mengambil bit dari data jpg Mengambil panjang dan lebar gambar
                    (`                      Embedding bit ${detail.bit} at pixel (${detail.x}, ${detail.y})<br>
                                            Red: ${detail.r}, Green: ${detail.g}, Blue: ${detail.b}<br>
                                            Step: ${detail.step}
                                        `);
                  } else if (jsonResponse.status === "progress") {
                    $("#progressBar").width(jsonResponse.progress + "%");
                    $("#progressBar").attr("aria-valuenow", jsonResponse.progress);
                    $("#progressStatus").text("Proses embedding: " + Math.round(jsonResponse.progress) + "%");
                  }
                } catch (e) {
                  console.log("Parsing error: ", e);
                }
              }
            }, false);

            return xhr;
          },
          success: function(response) {
            var res = JSON.parse(response);
            if (res.status === "success") {
              $("#progressBar").width("100%");
              $("#progressBar").attr("aria-valuenow", 100);
              $("#progressStatus").html("Embedding selesai! <a href='" + res.imagePath + "' download>Download gambar hasil embedding</a>");
            } else {
              $("#progressStatus").text("Error: " + res.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $("#progressStatus").text("Request failed: " + textStatus + " - " + errorThrown);
          }
        });
      });
    });
  </script>
</body>

</html>