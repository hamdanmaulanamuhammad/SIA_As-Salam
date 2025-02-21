<?php

require 'vendor/autoload.php';

use Maestroerror\HeicToJpg;

$filePath = 'storage/app/public/sample.heic';  // Ganti dengan lokasi file HEIC
$convertedPath = 'storage/app/public/sample.jpg';

try {
    $heic = HeicToJpg::convert($filePath);
    $heic->saveAs($convertedPath);

    if (file_exists($convertedPath)) {
        echo "✅ Konversi berhasil! File disimpan di: " . $convertedPath;
    } else {
        echo "❌ Konversi gagal, file tidak ditemukan.";
    }
} catch (Exception $e) {
    echo "⚠️ Error: " . $e->getMessage();
}