<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $img = public_path('Lambang_Kab_Kolaka.jpg');
    if(!file_exists($img)) $img = public_path('Lambang_Kab_Kolaka.PNG');
    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge($img, .3, true)->size(200)->generate('test');
    echo "SUCCESS";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
