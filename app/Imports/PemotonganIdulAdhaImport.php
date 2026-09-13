<?php

namespace App\Imports;

use App\Models\PemotonganIdulAdha;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PemotonganIdulAdhaImport implements ToCollection
{
    /**
     * Parse data excel idul adha secara fleksibel baik dari format laporan dinas (8 kolom)
     * maupun format template sistem (13 kolom).
     */
    public function collection(Collection $rows)
    {
        $lastTanggal = null;
        $colMap = [];

        foreach ($rows as $row) {
            $rowValues = array_map(function ($val) {
                return $val !== null ? trim((string)$val) : '';
            }, $row->toArray());

            // Check non empty values
            $nonEmptyValues = array_filter($rowValues, function ($val) {
                return $val !== '';
            });

            if (empty($nonEmptyValues)) {
                continue;
            }

            $rowString = strtolower(implode(' ', $nonEmptyValues));

            // Skip title/header rows of the document
            if (str_contains($rowString, 'laporan data') || str_contains($rowString, 'dinas perkebunan')) {
                continue;
            }

            // Check if this row is a column header row
            if (str_contains($rowString, 'tanggal') && (str_contains($rowString, 'lokasi') || str_contains($rowString, 'hewan') || str_contains($rowString, 'alamat') || str_contains($rowString, 'kec/desa'))) {
                $colMap = [];
                foreach ($rowValues as $idx => $val) {
                    $valLower = strtolower($val);
                    if (str_contains($valLower, 'no') && !str_contains($valLower, 'nama')) {
                        $colMap['no'] = $idx;
                    } elseif (str_contains($valLower, 'tanggal') || str_contains($valLower, 'tgl')) {
                        $colMap['tanggal'] = $idx;
                    } elseif (str_contains($valLower, 'nama lokasi') || str_contains($valLower, 'lokasi / instansi')) {
                        $colMap['nama_lokasi'] = $idx;
                    } elseif (str_contains($valLower, 'jenis lokasi') || str_contains($valLower, 'jenis_lokasi')) {
                        $colMap['jenis_lokasi'] = $idx;
                    } elseif (str_contains($valLower, 'alamat')) {
                        $colMap['alamat'] = $idx;
                    } elseif (str_contains($valLower, 'kec') || str_contains($valLower, 'kecamatan')) {
                        $colMap['kecamatan'] = $idx;
                    } elseif (str_contains($valLower, 'desa') || str_contains($valLower, 'kelurahan')) {
                        $colMap['desa'] = $idx;
                    } elseif (str_contains($valLower, 'hewan')) {
                        $colMap['jenis_hewan'] = $idx;
                    } elseif (str_contains($valLower, 'jumlah') || str_contains($valLower, 'jml')) {
                        $colMap['jumlah'] = $idx;
                    } elseif (str_contains($valLower, 'pelapor')) {
                        $colMap['pelapor'] = $idx;
                    } elseif (str_contains($valLower, 'upt')) {
                        $colMap['upt'] = $idx;
                    }
                }
                continue; // Skip the header row itself
            }

            // Try extracting date
            $rawTanggal = '';
            if (isset($colMap['tanggal']) && isset($rowValues[$colMap['tanggal']])) {
                $rawTanggal = $rowValues[$colMap['tanggal']];
            } else {
                // Fallback check col 1 then col 0
                if (isset($rowValues[1]) && $this->isDateString($rowValues[1])) {
                    $rawTanggal = $rowValues[1];
                } elseif (isset($rowValues[0]) && $this->isDateString($rowValues[0])) {
                    $rawTanggal = $rowValues[0];
                }
            }

            $parsedTanggal = $this->parseTanggal($rawTanggal);
            if ($parsedTanggal) {
                $lastTanggal = $parsedTanggal;
            }

            if (!$lastTanggal) {
                continue; // Skip if no valid date available
            }

            // Determine values according to available column structure
            if (count($rowValues) >= 12 && isset($colMap['nama_lokasi']) && isset($colMap['desa'])) {
                // Full 13-column template format
                $namaLokasi = $rowValues[$colMap['nama_lokasi'] ?? 1] ?? '-';
                if (empty($namaLokasi) || $namaLokasi === '-') continue;

                PemotonganIdulAdha::create([
                    'tanggal'              => $lastTanggal,
                    'nama_lokasi'          => $namaLokasi,
                    'jenis_lokasi'         => $rowValues[$colMap['jenis_lokasi'] ?? 2] ?? 'Lainnya',
                    'jenis_lokasi_lainnya' => $rowValues[$colMap['jenis_lokasi_lainnya'] ?? 3] ?? null,
                    'alamat'               => $rowValues[$colMap['alamat'] ?? 4] ?? '-',
                    'propinsi'             => !empty($rowValues[$colMap['propinsi'] ?? 5]) ? $rowValues[$colMap['propinsi'] ?? 5] : 'Sulawesi Tenggara',
                    'kabupaten'            => !empty($rowValues[$colMap['kabupaten'] ?? 6]) ? $rowValues[$colMap['kabupaten'] ?? 6] : 'Kolaka',
                    'kecamatan'            => $rowValues[$colMap['kecamatan'] ?? 7] ?? '-',
                    'desa'                 => $rowValues[$colMap['desa'] ?? 8] ?? '-',
                    'jenis_hewan'          => $rowValues[$colMap['jenis_hewan'] ?? 9] ?? '-',
                    'jumlah'               => isset($rowValues[$colMap['jumlah'] ?? 10]) ? (int)$rowValues[$colMap['jumlah'] ?? 10] : 1,
                    'upt'                  => $rowValues[$colMap['upt'] ?? 11] ?? '-',
                    'pelapor'              => $rowValues[$colMap['pelapor'] ?? 12] ?? '-',
                ]);
            } else {
                // Standard 8-column layout (from Dinas Excel: No, Tanggal, Jenis/Nama Lokasi, Alamat Lengkap, Kec/Desa, Jenis Hewan, Jumlah, Pelapor)
                $hasNoCol = (isset($colMap['no']) && $colMap['no'] >= 0) ||
                    (is_numeric($rowValues[0] ?? '') && (int)($rowValues[0] ?? 0) > 0 && isset($rowValues[1]) && $this->isDateString($rowValues[1]));

                $offset = $hasNoCol ? 1 : 0;

                $namaLokasi = $rowValues[$colMap['nama_lokasi'] ?? ($offset + 1)] ?? ($rowValues[$offset + 1] ?? '-');
                if (empty($namaLokasi) || $namaLokasi === '-') continue;

                $alamat = $rowValues[$colMap['alamat'] ?? ($offset + 2)] ?? ($rowValues[$offset + 2] ?? '-');
                $kecamatan = $rowValues[$colMap['kecamatan'] ?? ($offset + 3)] ?? ($rowValues[$offset + 3] ?? '-');
                $jenisHewan = $rowValues[$colMap['jenis_hewan'] ?? ($offset + 4)] ?? ($rowValues[$offset + 4] ?? '-');
                $rawJumlah = $rowValues[$colMap['jumlah'] ?? ($offset + 5)] ?? ($rowValues[$offset + 5] ?? 1);
                $jumlah = is_numeric($rawJumlah) ? (int)$rawJumlah : 1;
                $pelapor = $rowValues[$colMap['pelapor'] ?? ($offset + 6)] ?? ($rowValues[$offset + 6] ?? '-');

                $desa = $namaLokasi;
                $jenisLokasiData = $this->determineJenisLokasi($namaLokasi, $alamat);
                $upt = $this->determineUpt($pelapor, $kecamatan);

                PemotonganIdulAdha::create([
                    'tanggal'              => $lastTanggal,
                    'nama_lokasi'          => $namaLokasi,
                    'jenis_lokasi'         => $jenisLokasiData['jenis_lokasi'],
                    'jenis_lokasi_lainnya' => $jenisLokasiData['jenis_lokasi_lainnya'],
                    'alamat'               => $alamat,
                    'propinsi'             => 'Sulawesi Tenggara',
                    'kabupaten'            => 'Kolaka',
                    'kecamatan'            => $kecamatan,
                    'desa'                 => $desa,
                    'jenis_hewan'          => $jenisHewan,
                    'jumlah'               => $jumlah > 0 ? $jumlah : 1,
                    'upt'                  => $upt,
                    'pelapor'              => $pelapor,
                ]);
            }
        }
    }

    private function isDateString($val)
    {
        if (empty($val)) return false;
        if (is_numeric($val) && (float)$val > 30000) return true;
        return str_contains($val, '/') || str_contains($val, '-');
    }

    private function parseTanggal($value)
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            $num = (float)$value;
            if ($num > 30000 && $num < 60000) {
                try {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($num)->format('Y-m-d');
                } catch (\Throwable $e) {
                }
            }
        }

        $val = trim((string)$value);
        if (empty($val)) return null;

        $val = str_replace('.', '/', $val);

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', $val, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $year = $m[3];
            if (strlen($year) == 2) $year = "20" . $year;
            return "{$year}-{$month}-{$day}";
        }

        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $val, $m)) {
            $year = $m[1];
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $day = str_pad($m[3], 2, '0', STR_PAD_LEFT);
            return "{$year}-{$month}-{$day}";
        }

        $time = strtotime($val);
        if ($time && $time > 0) {
            return date('Y-m-d', $time);
        }

        return null;
    }

    private function determineJenisLokasi($namaLokasi, $alamat)
    {
        $combined = strtolower($namaLokasi . ' ' . $alamat);

        if (str_contains($combined, 'masjid') || str_contains($combined, 'musholla') || str_contains($combined, 'mesjid')) {
            return ['jenis_lokasi' => 'Masjid', 'jenis_lokasi_lainnya' => null];
        }

        if (str_contains($combined, 'lapangan')) {
            return ['jenis_lokasi' => 'Lapangan', 'jenis_lokasi_lainnya' => null];
        }

        if (str_contains($combined, 'sekolah') || str_contains($combined, 'yayasan') || str_contains($combined, 'pondok') || str_contains($combined, 'pesantren') || str_contains($combined, 'sdn') || str_contains($combined, 'smp') || str_contains($combined, 'sma')) {
            return ['jenis_lokasi' => 'Sekolah / Yayasan', 'jenis_lokasi_lainnya' => null];
        }

        if (str_contains($combined, 'instansi') || str_contains($combined, 'pemerintah') || str_contains($combined, 'kantor') || str_contains($combined, 'dinas') || str_contains($combined, 'pemda')) {
            return ['jenis_lokasi' => 'Instansi Pemerintah', 'jenis_lokasi_lainnya' => null];
        }

        return [
            'jenis_lokasi'         => 'Lainnya',
            'jenis_lokasi_lainnya' => !empty($alamat) && $alamat !== '-' ? $alamat : $namaLokasi
        ];
    }

    private function determineUpt($pelapor, $kecamatan)
    {
        if (empty($pelapor) || $pelapor === '-') {
            return !empty($kecamatan) && $kecamatan !== '-' ? 'UPT ' . $kecamatan : 'UPT Kolaka';
        }

        if (str_contains(strtolower($pelapor), 'upt')) {
            return $pelapor;
        }

        if (!empty($kecamatan) && $kecamatan !== '-') {
            return 'UPT ' . $kecamatan;
        }

        return 'UPT Kolaka';
    }
}
