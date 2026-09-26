import sys

with open(r'c:\laragon\www\rph\resources\views\laporan\cetak.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

target_info = '''            @if (!empty($kategori))
                Kategori Hewan &nbsp;&nbsp;&nbsp;: {{ $kategori }}
            @endif'''
replace_info = target_info + '''
            @if(isset($status_laporan) && $status_laporan == 'disetujui')
                <br>Status Data &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Disetujui (Lolos)
            @elseif(isset($status_laporan) && $status_laporan == 'ditolak')
                <br>Status Data &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Ditolak
            @endif'''
content = content.replace(target_info, replace_info)

target_th = '''                        <th>Umur</th>
                        <th>Berat</th>'''
replace_th = target_th + '''
                        @if(!isset($status_laporan) || $status_laporan == 'semua')
                            <th>Status</th>
                        @elseif($status_laporan == 'ditolak')
                            <th>Keterangan</th>
                        @endif'''
content = content.replace(target_th, replace_th)

target_td = '''                                <td>{{ $row->umur ?? '-' }}</td>
                                <td>{{ $row->berat ?? '-' }} Kg</td>'''
replace_td = target_td + '''
                                @if(!isset($status_laporan) || $status_laporan == 'semua')
                                    <td>{{ $row->status }}</td>
                                @elseif($status_laporan == 'ditolak')
                                    <td class="text-left" style="font-size: 8pt;">{{ $row->status }}</td>
                                @endif'''
content = content.replace(target_td, replace_td)

with open(r'c:\laragon\www\rph\resources\views\laporan\cetak.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
