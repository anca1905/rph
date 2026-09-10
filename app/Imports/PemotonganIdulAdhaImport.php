<?php

namespace App\Imports;

use App\Models\PemotonganIdulAdha;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PemotonganIdulAdhaImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    /**
     * Start reading from row 2 (assuming row 1 is the header).
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Hilangkan kolom kosong di awal jika converter excel menggeser kolom
        $row = array_values(array_filter($row, function($value) {
            return $value !== null && $value !== '';
        }));

        if (count($row) < 5) {
            return null; // Abaikan baris yang isinya terlalu sedikit (bukan data valid)
        }

        // Coba konversi tanggal excel atau format string ke Y-m-d
        $tanggal = $row[0] ?? null;
        if (is_numeric($tanggal)) {
            $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
        } else if ($tanggal) {
            // Coba parsing d/m/Y ke Y-m-d
            $parsed = date_parse_from_format('d/m/Y', $tanggal);
            if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
                $tanggal = $parsed['year'] . '-' . str_pad($parsed['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parsed['day'], 2, '0', STR_PAD_LEFT);
            } else {
                $tanggal = date('Y-m-d', strtotime($tanggal));
            }
        }

        return new PemotonganIdulAdha([
            'tanggal'              => $tanggal,
            'nama_lokasi'          => $row[1] ?? '-',
            'jenis_lokasi'         => $row[2] ?? '-',
            'jenis_lokasi_lainnya' => $row[3] ?? null,
            'alamat'               => $row[4] ?? '-',
            'propinsi'             => $row[5] ?? 'Sulawesi Tenggara',
            'kabupaten'            => $row[6] ?? 'Kolaka',
            'kecamatan'            => $row[7] ?? '-',
            'desa'                 => $row[8] ?? '-',
            'jenis_hewan'          => $row[9] ?? '-',
            'jumlah'               => isset($row[10]) ? (int)$row[10] : 0,
            'upt'                  => $row[11] ?? '-',
            'pelapor'              => $row[12] ?? '-',
        ]);
    }
}
