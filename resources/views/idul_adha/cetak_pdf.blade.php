<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Pemotongan Idul Adha</title>
    <style>
        @page {
            size: A4 {{ $orientasi ?? 'landscape' }};
            margin: 2.54cm;
        }

        @page WordSection1 {
            size: 841.9pt 595.3pt;
            mso-page-orientation: landscape;
            margin: 2.54cm;
        }

        div.WordSection1 {
            page: WordSection1;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.3;
        }

        .tabel-kop {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-kop td {
            padding: 0;
            border: none;
        }

        .teks-kop h1,
        .teks-kop h2 {
            margin: 0;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }

        .teks-kop h1 {
            font-size: 18pt;
        }

        .teks-kop h2 {
            font-size: 16pt;
        }

        .teks-kop p {
            margin: 4px 0 0 0;
            font-size: 14pt;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }

        .judul-laporan {
            text-align: center;
            font-size: 14pt;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .judul-laporan span.underline {
            text-decoration: underline;
        }

        .judul-laporan span.nomor-surat {
            display: block;
            font-size: 12pt;
            font-weight: normal;
            text-transform: none;
            margin-top: 2px;
        }

        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        table.tabel-data th,
        table.tabel-data td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            vertical-align: middle;
        }

        table.tabel-data th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
        }

        .kalimat-penutup {
            text-indent: 40px;
            margin-bottom: 15px;
            text-align: justify;
            font-size: 12pt;
            line-height: 1.5;
        }

        .tabel-ttd {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <div class="WordSection1">

        @php
            if (!function_exists('hapusLatarPutih')) {
                function hapusLatarPutih($filepath)
                {
                    if (!file_exists($filepath)) return '';
                    $imgString = file_get_contents($filepath);
                    if (!function_exists('imagecreatefromstring')) return $imgString;
                    $img = @imagecreatefromstring($imgString);
                    if (!$img) return $imgString;

                    $w = imagesx($img);
                    $h = imagesy($img);
                    $out = imagecreatetruecolor($w, $h);
                    imagesavealpha($out, true);
                    $trans = imagecolorallocatealpha($out, 0, 0, 0, 127);
                    imagefill($out, 0, 0, $trans);

                    for ($x = 0; $x < $w; $x++) {
                        for ($y = 0; $y < $h; $y++) {
                            $rgb = imagecolorat($img, $x, $y);
                            $colors = imagecolorsforindex($img, $rgb);
                            if ($colors['red'] > 200 && $colors['green'] > 200 && $colors['blue'] > 200) {
                                imagesetpixel($out, $x, $y, $trans);
                            } else {
                                imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $colors['red'], $colors['green'], $colors['blue'], $colors['alpha']));
                            }
                        }
                    }
                    ob_start();
                    imagepng($out);
                    $imgData = ob_get_clean();
                    imagedestroy($img);
                    imagedestroy($out);
                    return $imgData;
                }
            }

            $imagePath = public_path('Lambang_Kab_Kolaka.jpg');
            if (!file_exists($imagePath)) $imagePath = public_path('Lambang_Kab_Kolaka.PNG');
            $base64 = '';
            if (file_exists($imagePath)) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $dataImage = file_get_contents($imagePath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);
            }

            $ttdBase64 = '';
            if (isset($gambar_ttd) && $gambar_ttd != '' && file_exists(public_path('uploads/' . $gambar_ttd))) {
                $imgTransparan = hapusLatarPutih(public_path('uploads/' . $gambar_ttd));
                $ttdBase64 = 'data:image/png;base64,' . base64_encode($imgTransparan);
            }

            $stempelBase64 = '';
            if (isset($gambar_stempel) && $gambar_stempel != '' && file_exists(public_path('uploads/' . $gambar_stempel))) {
                $imgTransparan = hapusLatarPutih(public_path('uploads/' . $gambar_stempel));
                $stempelBase64 = 'data:image/png;base64,' . base64_encode($imgTransparan);
            }

            $arrayBulan = [
                '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
                '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII',
            ];
            $nomorSurat = '000/RPH-IA/DISBUNAK/KOLAKA/SULTRA/' . $arrayBulan[date('m')] . '/' . date('Y');
        @endphp

        <table class="tabel-kop">
            <tr>
                <td width="15%" align="center" valign="middle">
                    @if ($base64 != '')
                        <img src="{{ $base64 }}" width="90" alt="Logo Kolaka">
                    @endif
                </td>
                <td width="85%" class="teks-kop" align="center" valign="middle">
                    <h1>PEMERINTAH KABUPATEN KOLAKA</h1>
                    <h2>DINAS PERKEBUNAN DAN PETERNAKAN</h2>
                    <p>Jalan Badewi Nomor 75, Kolaka 93517</p>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 20px;">
            <tr><td style="border-bottom: 3px solid black;"></td></tr>
            <tr><td style="border-bottom: 1px solid black; padding-top: 2px;"></td></tr>
        </table>

        <div class="judul-laporan">
            <span class="underline">LAPORAN DATA PEMOTONGAN IDUL ADHA</span>
            <span class="nomor-surat">No. {{ $nomorSurat }}</span>
        </div>

        <div style="margin-bottom: 10px; font-weight: bold; font-size: 12pt;">
            Tanggal Diunduh : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} <br>
        </div>

        <table class="tabel-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Lokasi / Instansi</th>
                    <th>Jenis Lokasi</th>
                    <th>Alamat Lengkap</th>
                    <th>Kec. / Desa</th>
                    <th>Jenis Hewan</th>
                    <th>Jumlah</th>
                    <th>Pelapor (UPT)</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($dataLaporan) && count($dataLaporan) > 0)
                    @foreach ($dataLaporan as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="text-left font-bold">{{ $row->nama_lokasi }}</td>
                            <td>
                                {{ $row->jenis_lokasi }}
                                @if($row->jenis_lokasi == 'Lainnya' && $row->jenis_lokasi_lainnya)
                                    <br><span style="font-size: 8pt; color: #666;">({{ $row->jenis_lokasi_lainnya }})</span>
                                @endif
                            </td>
                            <td class="text-left" style="font-size: 9pt;">{{ $row->alamat }}</td>
                            <td>
                                <strong>{{ $row->kecamatan }}</strong><br>
                                <span style="font-size: 8pt;">{{ $row->desa }}</span>
                            </td>
                            <td>{{ $row->jenis_hewan }}</td>
                            <td style="font-weight: bold; font-size: 12pt;">{{ $row->jumlah }}</td>
                            <td>
                                <strong>{{ $row->pelapor }}</strong><br>
                                <span style="font-size: 8pt;">{{ $row->upt }}</span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="padding: 20px;">Belum ada data laporan Idul Adha.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="kalimat-penutup">
            Demikian laporan pemotongan hewan kurban Idul Adha ini dibuat dan disusun dengan sebenar-benarnya berdasarkan data operasional yang tercatat untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <table class="tabel-ttd">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="left" style="font-size: 12pt; line-height: 1.2;">
                    Kepala Dinas Perkebunan dan Peternakan<br>
                    Kabupaten Kolaka<br>
                    
                    <div style="margin: 5px 0; height: 110px; position: relative; display: block;">
                        @if(isset($qrCode))
                            <img src="{{ $qrCode }}" alt="QR Code" width="100" style="position: absolute; top: 5px; left: 0; z-index: 1;">
                            @if(isset($base64) && $base64 != '')
                                <img src="{{ $base64 }}" width="20" style="position: absolute; top: 45px; left: 40px; background-color: white; z-index: 2; padding: 2px;">
                            @endif
                        @else
                            @if ($stempelBase64 != '')
                                <img src="{{ $stempelBase64 }}" width="95" alt="Stempel"
                                    style="position: absolute; top: 0; left: 0; z-index: 1;">
                            @endif
                            @if ($ttdBase64 != '')
                                <img src="{{ $ttdBase64 }}" width="130" alt="TTD"
                                    style="position: absolute; top: 5px; left: 40px; z-index: 2;">
                            @endif
                        @endif
                    </div>

                    {{ $nama_ttd ?? 'Hasbir Jaya Razak, SP' }}<br>
                    {{ $pangkat_ttd ?? 'Pembina Utama Muda, Gol. IV/c' }}<br>
                    NIP. {{ $nip_ttd ?? '19690914 199803 2 005' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
