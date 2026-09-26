import sys

with open(r'c:\laragon\www\rph\resources\views\laporan\cetak.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

target_info = '''
            @if(isset($status_laporan) && $status_laporan == 'disetujui')
                <br>Status Data &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Disetujui (Lolos)
            @elseif(isset($status_laporan) && $status_laporan == 'ditolak')
                <br>Status Data &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Ditolak
            @endif'''
content = content.replace(target_info, '')

target_th = '''
                        @if(!isset($status_laporan) || $status_laporan == 'semua')
                            <th>Status</th>
                        @elseif($status_laporan == 'ditolak')
                            <th>Keterangan</th>
                        @endif'''
content = content.replace(target_th, '')

target_td = '''
                                @if(!isset($status_laporan) || $status_laporan == 'semua')
                                    <td>{{ $row->status }}</td>
                                @elseif($status_laporan == 'ditolak')
                                    <td class="text-left" style="font-size: 8pt;">{{ $row->status }}</td>
                                @endif'''
content = content.replace(target_td, '')

# Change @if ($jenis_laporan == 'hewan') to in_array
content = content.replace("@if ($jenis_laporan == 'hewan')", "@if (in_array($jenis_laporan, ['hewan', 'hewan_ditolak']))")

target_th_2 = '''                        <th>Umur</th>
                        <th>Berat</th>'''
replace_th_2 = target_th_2 + '''
                        @if($jenis_laporan == 'hewan_ditolak')
                            <th>Keterangan</th>
                        @endif'''
content = content.replace(target_th_2, replace_th_2)

target_td_2 = '''                                <td>{{ $row->umur ?? '-' }}</td>
                                <td>{{ $row->berat ?? '-' }} Kg</td>'''
replace_td_2 = target_td_2 + '''
                                @if($jenis_laporan == 'hewan_ditolak')
                                    <td class="text-left" style="font-size: 8pt;">{{ $row->status }}</td>
                                @endif'''
content = content.replace(target_td_2, replace_td_2)

# Change title
target_title = '''            @else
                <span class="underline">LAPORAN DATA {{ str_replace('_', ' ', strtoupper($jenis_laporan)) }}</span>
                <span class="nomor-surat">No. {{ $nomorSurat }}</span>
            @endif'''
replace_title = '''            @else
                <span class="underline">LAPORAN DATA {{ strtoupper(str_replace('_', ' ', $jenis_laporan == 'hewan_ditolak' ? 'hewan_ditolak' : $jenis_laporan)) }}</span>
                <span class="nomor-surat">No. {{ $nomorSurat }}</span>
            @endif'''
content = content.replace(target_title, replace_title)

with open(r'c:\laragon\www\rph\resources\views\laporan\cetak.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
