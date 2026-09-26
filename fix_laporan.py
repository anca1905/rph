import sys
import re

with open(r'c:\laragon\www\rph\app\Http\Controllers\LaporanController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Revert previous parameter changes
content = re.sub(
    r'private function getLaporanData\(\$jenis_laporan, \$start_date, \$end_date, \$kategori, \$status_laporan = ""\)',
    r'private function getLaporanData($jenis_laporan, $start_date, $end_date, $kategori)',
    content
)

# Revert where clause
target_where = '''
                if ($jenis_laporan == 'hewan' && $status_laporan == 'ditolak') {
                    $query->where('status', 'like', '%Ditolak%');
                } elseif ($jenis_laporan == 'hewan' && $status_laporan == 'disetujui') {
                    $query->where('status', 'not like', '%Ditolak%');
                }
                
                $dataLaporan = $query->latest($dateField)->get();
            }'''
content = content.replace(target_where, '''
                $dataLaporan = $query->latest($dateField)->get();
            }''')

# Now add hewan_ditolak to getLaporanData switch statement
target_switch = '''            switch ($jenis_laporan) {
                case 'hewan':
                    $query = Hewan::where('kategori', 'Hewan Harian');
                    $dateField = 'tanggal_masuk';
                    break;'''
replace_switch = '''            switch ($jenis_laporan) {
                case 'hewan':
                    $query = Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%');
                    $dateField = 'tanggal_masuk';
                    break;
                case 'hewan_ditolak':
                    $query = Hewan::where('kategori', 'Hewan Harian')->where('status', 'like', '%Ditolak%');
                    $dateField = 'tanggal_masuk';
                    break;'''
content = content.replace(target_switch, replace_switch)

# Revert $status_laporan in index
target_index = '''        $kategori = $request->input("kategori", "");
        $status_laporan = $request->input("status_laporan", "");
        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori, $status_laporan);

        return view("laporan.index", compact("jenis_laporan", "start_date", "end_date", "kategori", "status_laporan", "dataLaporan"));'''
replace_index = '''        $kategori = $request->input('kategori', '');
        
        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori);

        return view('laporan.index', compact('jenis_laporan', 'start_date', 'end_date', 'kategori', 'dataLaporan'));'''
content = content.replace(target_index, replace_index)

# Revert $status_laporan in export
target_export = '''        $format = $request->input("format");

        $status_laporan = $request->input("status_laporan", "");
        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori, $status_laporan);'''
replace_export = '''        $format = $request->input('format');

        $dataLaporan = $this->getLaporanData($jenis_laporan, $start_date, $end_date, $kategori);'''
content = content.replace(target_export, replace_export)

target_export_data = '''"kategori"       => $kategori,
            "status_laporan" => $status_laporan,
            "dataLaporan"    => $dataLaporan,'''
replace_export_data = '''            'kategori'       => $kategori,
            'dataLaporan'    => $dataLaporan,'''
content = content.replace(target_export_data, replace_export_data)

with open(r'c:\laragon\www\rph\app\Http\Controllers\LaporanController.php', 'w', encoding='utf-8') as f:
    f.write(content)
