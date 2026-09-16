<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$req = request();
$req->merge(['jenis_laporan' => 'hewan', 'format' => 'pdf']);
try {
    $res = app('App\Http\Controllers\LaporanController')->export($req);
    echo get_class($res);
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
