<?php
if(isset($_GET['image'])) {
    $imagePath = $_GET['image'];
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $imagePath . '"');
    readfile($imagePath);
    exit;
}
?>
