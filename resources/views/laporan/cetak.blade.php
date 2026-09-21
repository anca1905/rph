<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan RPH</title>
    <style>
        /* Mengatur Kertas A4 Landscape dengan Margin Normal (2.54cm / 1 inci di semua sisi) */
        @page {
            size: A4 {{ $orientasi ?? 'landscape' }};
            margin: 2.54cm;
        }

        /* Trik khusus MS Word untuk memaksa halaman dokumen menjadi Landscape & Margin Normal */
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

        /* Styling Kop Surat */
        .tabel-kop {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-kop td {
            padding: 0;
            border: none;
        }

        .teks-kop h1,
        .teks-kop h2,
        .teks-kop h3 {
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

        /* Konten Laporan */
        .judul-laporan {
            text-align: center;
            font-size: 14pt;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 10px;
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

        .info-filter {
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 12pt;
        }

        /* Tabel Laporan */
        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
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

        .text-right {
            text-align: right !important;
        }

        /* Penutup */
        .kalimat-penutup {
            text-indent: 40px;
            margin-bottom: 15px;
            text-align: justify;
            font-size: 12pt;
            line-height: 1.5;
        }

        /* Hilangkan page-break-inside untuk menghindari bug lompat halaman di DomPDF */
        .tabel-ttd {
            width: 100%;
            border-collapse: collapse;
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

            // 3. Format Bulan Romawi & Tahun
            $arrayBulan = [
                '01' => 'I',
                '02' => 'II',
                '03' => 'III',
                '04' => 'IV',
                '05' => 'V',
                '06' => 'VI',
                '07' => 'VII',
                '08' => 'VIII',
                '09' => 'IX',
                '10' => 'X',
                '11' => 'XI',
                '12' => 'XII',
            ];
            $nomorSurat = '000/RPH/DISBUNAK/KOLAKA/SULTRA/' . $arrayBulan[date('m')] . '/' . date('Y');
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
                    <p>
                        Jalan Badewi Nomor 75, Kolaka 93517
                    </p>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 20px;">
            <tr>
                <td style="border-bottom: 3px solid black;"></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black; padding-top: 2px;"></td>
            </tr>
        </table>

        <div class="judul-laporan">
            @if ($jenis_laporan == 'pengawasan')
                <span class="underline">LAPORAN HARIAN PENGAWASAN TERNAK</span><br>
                <span>DI RUMAH POTONG HEWAN (RPH) {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F Y')) }}</span>
            @else
                <span class="underline">LAPORAN DATA {{ str_replace('_', ' ', strtoupper($jenis_laporan)) }}</span>
                <span class="nomor-surat">No. {{ $nomorSurat }}</span>
            @endif
        </div>

        <div class="info-filter">
            Tanggal Diunduh : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} <br>
            @if (!empty($kategori))
                Kategori Hewan &nbsp;&nbsp;&nbsp;: {{ $kategori }}
            @endif
        </div>

        @if ($jenis_laporan == 'pengawasan')
            <table class="tabel-data">
                <thead>
                    <tr>
                        <th rowspan="3">NO.</th>
                        <th rowspan="3">TANGGAL</th>
                        <th rowspan="3">NAMA PEDAGANG</th>
                        <th colspan="4">JENIS TERNAK</th>
                        <th rowspan="3">PERKIRAAN BH</th>
                        <th colspan="2">KETERANGAN</th>
                    </tr>
                    <tr>
                        <th colspan="2">Sapi</th>
                        <th colspan="2">Kerbau</th>
                        <th rowspan="2">Asal Ternak/<br>Surat Pengantar</th>
                        <th rowspan="2">AM/PM</th>
                    </tr>
                    <tr>
                        <th>&#9794;</th>
                        <th>&#9792;</th>
                        <th>&#9794;</th>
                        <th>&#9792;</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($dataLaporan) && count($dataLaporan) > 0)
                        @php
                            $grouped = [];
                            foreach($dataLaporan as $item) {
                                $date = \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/y');
                                $pedagang = $item->nama_pemilik;
                                if (!isset($grouped[$date])) {
                                    $grouped[$date] = [];
                                }
                                if (!isset($grouped[$date][$pedagang])) {
                                    $grouped[$date][$pedagang] = [
                                        'sapi_jantan' => 0,
                                        'sapi_betina' => 0,
                                        'kerbau_jantan' => 0,
                                        'kerbau_betina' => 0,
                                        'berat' => [],
                                        'asal' => $item->asal_hewan,
                                        'ampm' => 'N'
                                    ];
                                }
                                
                                $jenis = strtolower(trim($item->jenis_hewan));
                                $kelamin = strtolower(trim($item->jenis_kelamin));
                                if ($jenis == 'sapi' && $kelamin == 'jantan') {
                                    $grouped[$date][$pedagang]['sapi_jantan']++;
                                } elseif ($jenis == 'sapi' && $kelamin == 'betina') {
                                    $grouped[$date][$pedagang]['sapi_betina']++;
                                } elseif ($jenis == 'kerbau' && $kelamin == 'jantan') {
                                    $grouped[$date][$pedagang]['kerbau_jantan']++;
                                } elseif ($jenis == 'kerbau' && $kelamin == 'betina') {
                                    $grouped[$date][$pedagang]['kerbau_betina']++;
                                }

                                if ($item->berat) {
                                    $grouped[$date][$pedagang]['berat'][] = $item->berat;
                                }

                                // Status AM/PM
                                $status = 'N';
                                if ($item->antemortem && stripos($item->antemortem->status_antemortem, 'ditolak') !== false) {
                                    $status = 'Ditolak(AM)';
                                }
                                if ($item->postmortem && stripos($item->postmortem->kondisi_karkas, 'afkir') !== false) {
                                    $status = 'Afkir(PM)';
                                }
                                if ($status != 'N') {
                                    $grouped[$date][$pedagang]['ampm'] = $status;
                                }
                            }
                            $no = 1;
                        @endphp

                        @foreach($grouped as $date => $pedagangs)
                            @php
                                $isFirstDate = true;
                                $rowspanDate = count($pedagangs);
                            @endphp
                            @foreach($pedagangs as $pedagang => $data)
                                <tr>
                                    @if($isFirstDate)
                                        <td rowspan="{{ $rowspanDate }}">{{ $no++ }}.</td>
                                        <td rowspan="{{ $rowspanDate }}">{{ $date }}</td>
                                        @php $isFirstDate = false; @endphp
                                    @endif
                                    <td class="text-left">{{ $pedagang }}</td>
                                    <td>{{ $data['sapi_jantan'] > 0 ? $data['sapi_jantan'] : '' }}</td>
                                    <td>{{ $data['sapi_betina'] > 0 ? $data['sapi_betina'] : '' }}</td>
                                    <td>{{ $data['kerbau_jantan'] > 0 ? $data['kerbau_jantan'] : '' }}</td>
                                    <td>{{ $data['kerbau_betina'] > 0 ? $data['kerbau_betina'] : '' }}</td>
                                    <td>
                                        @php
                                            if (count($data['berat']) > 0) {
                                                $min = min($data['berat']);
                                                $max = max($data['berat']);
                                                echo $min == $max ? $min . ' kg' : $min . '-' . $max . ' kg';
                                            } else {
                                                echo '-';
                                            }
                                        @endphp
                                    </td>
                                    <td>{{ $data['asal'] }}</td>
                                    <td>{{ $data['ampm'] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 20px;">Tidak ada data laporan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
        <table class="tabel-data">
            <thead>
                <tr>
                    <th>No</th>
                    @if ($jenis_laporan == 'hewan')
                        <th>No Registrasi</th>
                        <th>Tgl Masuk</th>
                        <th>Nama Pemilik</th>
                        <th>Asal Hewan</th>
                        <th>Kategori</th>
                        <th>Jenis Hewan</th>
                        <th>Kelamin</th>
                        <th>Umur</th>
                        <th>Berat</th>
                    @elseif($jenis_laporan == 'idul_adha')
                        <th>Tanggal</th>
                        <th>Lokasi / Instansi</th>
                        <th>Jenis Lokasi</th>
                        <th>Alamat Lengkap</th>
                        <th>Kec. / Desa</th>
                        <th>Jenis Hewan</th>
                        <th>Jumlah</th>
                        <th>Pelapor (UPT)</th>
                    @elseif($jenis_laporan == 'pembayaran')
                        <th>No Registrasi</th>
                        <th>Nama Pemilik</th>
                        <th>Asal Hewan</th>
                        <th>Jenis Hewan</th>
                        <th>Kategori</th>
                        <th>Tgl Transaksi</th>
                        <th>Total Biaya</th>
                        <th>Status Bayar</th>
                    @elseif($jenis_laporan == 'antemortem')
                        <th>No Registrasi</th>
                        <th>Nama Pemilik</th>
                        <th>Asal Hewan</th>
                        <th>Jenis Hewan</th>
                        <th>Kategori</th>
                        <th>Umur</th>
                        <th>Berat</th>
                        <th>Tgl Periksa</th>
                        <th>Kondisi Fisik</th>
                        <th>Tanda Penyakit</th>
                        <th>Status AM</th>
                    @elseif($jenis_laporan == 'pemotongan')
                        <th>No Registrasi</th>
                        <th>Nama Pemilik</th>
                        <th>Asal Hewan</th>
                        <th>Jenis Hewan</th>
                        <th>Kategori</th>
                        <th>Waktu Potong</th>
                        <th>Karkas (Kg)</th>
                        <th>Jeroan (Kg)</th>
                        <th>Status Potong</th>
                    @elseif($jenis_laporan == 'postmortem')
                        <th>No Registrasi</th>
                        <th>Nama Pemilik</th>
                        <th>Asal Hewan</th>
                        <th>Jenis Hewan</th>
                        <th>Kategori</th>
                        <th>Waktu Periksa</th>
                        <th>Karkas</th>
                        <th>Jeroan</th>
                        <th>Limpa</th>
                        <th>Hati</th>
                        <th>Daging</th>
                        <th>Paru</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if (isset($dataLaporan) && count($dataLaporan) > 0)
                    @foreach ($dataLaporan as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if ($jenis_laporan == 'hewan')
                                <td>{{ $row->no_registrasi }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                                <td class="text-left">{{ $row->nama_pemilik }}</td>
                                <td class="text-left">{{ $row->asal_hewan }}</td>
                                <td>{{ $row->kategori }}</td>
                                <td>{{ $row->jenis_hewan }}</td>
                                <td>{{ $row->jenis_kelamin }}</td>
                                <td>{{ $row->umur ?? '-' }}</td>
                                <td>{{ $row->berat ?? '-' }} Kg</td>
                            @elseif($jenis_laporan == 'idul_adha')
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
                            @elseif($jenis_laporan == 'pembayaran')
                                <td>{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->kategori ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                                <td class="text-right">Rp {{ number_format($row->total_pembayaran, 0, ',', '.') }}</td>
                                <td>{{ $row->status_pembayaran }}</td>
                            @elseif($jenis_laporan == 'antemortem')
                                <td>{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->kategori ?? '-' }}</td>
                                <td>{{ $row->hewan->umur ?? '-' }}</td>
                                <td>{{ $row->hewan->berat ?? '-' }} Kg</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_periksa)->format('d/m/Y') }}</td>
                                <td>{{ $row->kondisi_fisik }}</td>
                                <td>{{ $row->tanda_penyakit }}</td>
                                <td>{{ $row->status_antemortem }}</td>
                            @elseif($jenis_laporan == 'pemotongan')
                                <td>{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->kategori ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->waktu_potong)->format('d/m/Y H:i') }}</td>
                                <td>{{ $row->berat_karkas }} Kg</td>
                                <td>{{ $row->berat_jeroan ?? '-' }} Kg</td>
                                <td>{{ $row->status_pemotongan }}</td>
                            @elseif($jenis_laporan == 'postmortem')
                                <td>{{ $row->hewan->no_registrasi ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->nama_pemilik ?? '-' }}</td>
                                <td class="text-left">{{ $row->hewan->asal_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->jenis_hewan ?? '-' }}</td>
                                <td>{{ $row->hewan->kategori ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->waktu_periksa)->format('d/m/Y H:i') }}</td>
                                <td>{{ $row->kondisi_karkas }}</td>
                                <td>{{ $row->kondisi_jeroan }}</td>
                                <td>{{ $row->limpa }}</td>
                                <td>{{ $row->hati }}</td>
                                <td>{{ $row->daging }}</td>
                                <td>{{ $row->paru_paru }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" style="padding: 20px;">Tidak ada data untuk filter tersebut.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @endif

        <div class="kalimat-penutup">
            Demikian laporan ini dibuat dan disusun dengan sebenar-benarnya berdasarkan data operasional yang tercatat
            pada Sistem Informasi Rumah Potong Hewan (RPH) Kabupaten Kolaka untuk dapat dipergunakan sebagaimana
            mestinya.
        </div>

        <table class="tabel-ttd">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="left" style="font-size: 12pt; line-height: 1.2;">
                    {{ $jabatan_ttd ?? 'Kepala Bidang Peternakan' }}<br>
                    Kabupaten Kolaka<br>
                    
                    <div style="margin: 5px 0; height: 110px; position: relative; display: block;">
                        @if(!empty($qrCode))
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
