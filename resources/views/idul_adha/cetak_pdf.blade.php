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
            $logoPath = public_path('Lambang_Kab_Kolaka.png');
            $base64 = '';
            if (file_exists($logoPath)) {
                $base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            $ttdBase64 = '';
            if (isset($gambar_ttd) && $gambar_ttd != '' && file_exists(public_path('uploads/' . $gambar_ttd))) {
                $ttdBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('uploads/' . $gambar_ttd)));
            }

            $stempelBase64 = '';
            if (isset($gambar_stempel) && $gambar_stempel != '' && file_exists(public_path('uploads/' . $gambar_stempel))) {
                $stempelBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('uploads/' . $gambar_stempel)));
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
                        @if(request('format') == 'word')
                            <img src="{{ $base64 }}" width="90" height="115" alt="Logo Kolaka">
                        @else
                            <img src="{{ $base64 }}" width="90" alt="Logo Kolaka">
                        @endif
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
                    {{ $jabatan_ttd ?? 'Kepala Bidang Peternakan' }}<br>
                    Kabupaten Kolaka<br>
                    
                    <div style="margin: 5px 0; height: 110px; position: relative; display: block;">
                        @if(isset($qrCode))
                            @if(request('format') == 'word')
                                <img src="{{ $qrCode }}" alt="QR Code" width="115" height="115" style="margin-top: 5px;">
                            @else
                                <img src="{{ $qrCode }}" alt="QR Code" width="120" height="120" style="position: absolute; top: 0; left: 0; z-index: 1;">
                                @if(isset($base64) && $base64 != '')
                                    <img src="{{ $base64 }}" width="15" height="20" style="position: absolute; top: 50px; left: 52px; background-color: white; z-index: 2; padding: 1px; border-radius: 2px;">
                                @endif
                            @endif
                        @else
                            @if ($stempelBase64 != '')
                                <img src="{{ $stempelBase64 }}" width="95" height="95" alt="Stempel"
                                    style="position: absolute; top: 0; left: 0; z-index: 1;">
                            @endif
                            @if ($ttdBase64 != '')
                                <img src="{{ $ttdBase64 }}" width="130" height="80" alt="TTD"
                                    style="position: absolute; top: 5px; left: 40px; z-index: 2;">
                            @endif
                        @endif
                    </div>

                    {{ $nama_ttd ?? 'Dr. drh. KASMAWATI, MM' }}<br>
                    {{ $pangkat_ttd ?? 'Pembina TK.I Gol. IV/b' }}<br>
                    NIP. {{ $nip_ttd ?? '19771202 200604 2 005' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
