import sys
import re

with open(r'c:\laragon\www\rph\app\Http\Controllers\LaporanController.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = re.sub(
    r'private function getLaporanData\(\$jenis_laporan, \$start_date, \$end_date, \$kategori\)',
    r'private function getLaporanData($jenis_laporan, $start_date, $end_date, $kategori, $status_laporan = "")',
    content
)

replace_get = '''
                if ($jenis_laporan == 'hewan' && $status_laporan == 'ditolak') {
                    $query->where('status', 'like', '%Ditolak%');
                } elseif ($jenis_laporan == 'hewan' && $status_laporan == 'disetujui') {
                    $query->where('status', 'not like', '%Ditolak%');
                }
                
                $dataLaporan = $query->latest($dateField)->get();
            }'''
content = re.sub(
    r'\$dataLaporan = \$query->latest\(\$dateField\)->get\(\);\s*\}',
    replace_get,
    content
)

content = re.sub(
    r'\$kategori = \$request->input\(\'kategori\', \'\'\);\s*\$dataLaporan = \$this->getLaporanData\(\$jenis_laporan, \$start_date, \$end_date, \$kategori\);',
    r'$kategori = $request->input("kategori", "");\n        $status_laporan = $request->input("status_laporan", "");\n        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori, $status_laporan);',
    content
)

content = re.sub(
    r'return view\(\'laporan\.index\', compact\(\'jenis_laporan\', \'start_date\', \'end_date\', \'kategori\', \'dataLaporan\'\)\);',
    r'return view("laporan.index", compact("jenis_laporan", "start_date", "end_date", "kategori", "status_laporan", "dataLaporan"));',
    content
)

content = re.sub(
    r'\$format = \$request->input\(\'format\'\);\s*\$dataLaporan = \$this->getLaporanData\(\$jenis_laporan, \$start_date, \$end_date, \$kategori\);',
    r'$format = $request->input("format");\n\n        $status_laporan = $request->input("status_laporan", "");\n        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori, $status_laporan);',
    content
)

content = re.sub(
    r'\'kategori\'\s*=> \$kategori,\s*\'dataLaporan\'\s*=> \$dataLaporan,',
    r'"kategori"       => $kategori,\n            "status_laporan" => $status_laporan,\n            "dataLaporan"    => $dataLaporan,',
    content
)

with open(r'c:\laragon\www\rph\app\Http\Controllers\LaporanController.php', 'w', encoding='utf-8') as f:
    f.write(content)
