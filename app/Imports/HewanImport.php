<?php

namespace App\Imports;

use App\Models\Hewan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class HewanImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Data bisa mulai di baris 2 atau 3, akan difilter
    }

    public function collection(Collection $rows)
    {
        $lastTanggal = null;
        $prefix = 'PH-';

        foreach ($rows as $row) {
            // Karena converter Excel kadang berantakan, cari indeks yang relevan.
            // Asumsi: A=No, B=Tanggal, C=Nama, D=Sapi Jantan, E=Sapi Betina, F=Kerbau Jantan, G=Kerbau Betina, H=Berat, I=Asal, J=AM/PM
            // Converter bisa menggeser jika ada baris kosong.
            
            // Filter nilai null agar tidak terjadi index error
            $rowValues = array_values(array_map(function($val) {
                return $val !== null ? trim($val) : '';
            }, $row->toArray()));

            if (count($rowValues) < 5) continue; // Skip header atau baris kosong
            
            // Coba temukan tanggal di kolom A atau B
            $tanggal = $rowValues[0];
            if (empty($tanggal) || is_numeric($tanggal) && $tanggal < 100) {
                // Jika A adalah nomor urut, tanggal mungkin di B
                $tanggal = $rowValues[1] ?? '';
            }
            
            // Jika tanggal berisi tanggal valid, update lastTanggal
            if ($tanggal && (str_contains($tanggal, '/') || str_contains($tanggal, '-') || is_numeric($tanggal) && $tanggal > 30000)) {
                if (is_numeric($tanggal)) {
                    $lastTanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                } else {
                    $tanggalStr = str_replace(['.'], '/', $tanggal);
                    // parsing d/m/Y atau d/m/y
                    $parts = explode('/', $tanggalStr);
                    if (count($parts) == 3) {
                        $y = $parts[2];
                        if (strlen($y) == 2) $y = "20" . $y;
                        $lastTanggal = $y . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                    } else {
                        $lastTanggal = date('Y-m-d', strtotime($tanggalStr));
                    }
                }
            }

            if (!$lastTanggal) continue; // Belum ada tanggal, mungkin baris header

            $nama = $rowValues[2] ?? '';
            if (empty($nama) || strtolower($nama) == 'nama pedagang' || strtolower($nama) == 'sapi') {
                continue; // Skip header
            }

            // Jumlah (Bisa sapi jantan, betina, kerbau jantan, betina)
            $sapiJantan = (int)($rowValues[3] ?? 0);
            $sapiBetina = (int)($rowValues[4] ?? 0);
            $kerbauJantan = (int)($rowValues[5] ?? 0);
            $kerbauBetina = (int)($rowValues[6] ?? 0);

            // Perkiraan BH (Berat Hidup)
            $beratRaw = (string) ($rowValues[7] ?? '0');
            
            // Ekstrak semua angka dari string
            preg_match_all('/\d+/', $beratRaw, $matches);
            $weights = [];
            foreach ($matches[0] as $match) {
                // Jika Excel menggabungkan angka tanpa pemisah (misal: "300200250" atau "250300")
                if (strlen($match) >= 6 && strlen($match) % 3 == 0) {
                    $chunks = str_split($match, 3); // Pecah per 3 digit: 300, 200, 250
                    $weights = array_merge($weights, $chunks);
                } else if (strlen($match) > 4) {
                    // Jika ada angka acak yang sangat besar, kita batasi maksimal 9999 agar DB tidak crash
                    $weights[] = 9999;
                } else {
                    $weights[] = $match;
                }
            }

            if (empty($weights)) {
                $weights = [0];
            }

            $asal = $rowValues[8] ?? 'Kolaka';
            if (empty($asal)) $asal = 'Kolaka';

            $ampm = $rowValues[9] ?? 'N';

            $totalHewan = $sapiJantan + $sapiBetina + $kerbauJantan + $kerbauBetina;
            if ($totalHewan == 0) continue;

            $weightIndex = 0; // Untuk melacak berat per ekor hewan
            $insertHewan = function($jenis, $kelamin, $count) use ($lastTanggal, $nama, &$weightIndex, $weights, $asal, $prefix) {
                for ($i = 0; $i < $count; $i++) {
                    // Ambil berat untuk hewan ini (jika ada beberapa berat misal 250,200,300)
                    $currentWeight = isset($weights[$weightIndex]) ? $weights[$weightIndex] : end($weights);
                    $weightIndex++;

                    // Generate No Reg
                    $lastHewan = Hewan::where('no_registrasi', 'like', $prefix . '%')
                                    ->orderBy('id_hewan', 'desc')
                                    ->first();
                    
                    $newNumber = $lastHewan ? str_pad(intval(substr($lastHewan->no_registrasi, 3)) + 1, 3, '0', STR_PAD_LEFT) : '001';
                    $noRegistrasi = $prefix . $newNumber;

                    Hewan::create([
                        'no_registrasi' => $noRegistrasi,
                        'tanggal_masuk' => $lastTanggal,
                        'nama_pemilik'  => $nama,
                        'jenis_hewan'   => $jenis,
                        'jenis_kelamin' => $kelamin,
                        'berat'         => (float)$currentWeight,
                        'asal_hewan'    => $asal,
                        'kategori'      => 'Hewan Harian',
                        'status'        => 'Menunggu Antemortem',
                    ]);
                }
            };

            if ($sapiJantan > 0) $insertHewan('Sapi', 'Jantan', $sapiJantan);
            if ($sapiBetina > 0) $insertHewan('Sapi', 'Betina', $sapiBetina);
            if ($kerbauJantan > 0) $insertHewan('Kerbau', 'Jantan', $kerbauJantan);
            if ($kerbauBetina > 0) $insertHewan('Kerbau', 'Betina', $kerbauBetina);
        }
    }
}
