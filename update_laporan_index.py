import sys
import re

with open(r'c:\laragon\www\rph\resources\views\laporan\index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Change grid-cols-4 to grid-cols-5
content = content.replace('grid-cols-1 md:grid-cols-4', 'grid-cols-1 md:grid-cols-5')

# Add Status filter dropdown
filter_html = '''
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Status Laporan (Khusus Hewan)</label>
                    <select name="status_laporan"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-slate-700 font-medium text-sm rounded-xl focus:outline-none focus:border-green-500 transition-colors shadow-inner">
                        <option value="semua" {{ (isset($status_laporan) && $status_laporan == 'semua') || empty($status_laporan) ? 'selected' : '' }}>Semua Status</option>
                        <option value="disetujui" {{ isset($status_laporan) && $status_laporan == 'disetujui' ? 'selected' : '' }}>Hanya Disetujui / Lolos</option>
                        <option value="ditolak" {{ isset($status_laporan) && $status_laporan == 'ditolak' ? 'selected' : '' }}>Hanya Ditolak</option>
                    </select>
                </div>
'''
content = content.replace('</select>\n                </div>\n\n                <div class="flex gap-2">', '</select>\n                </div>\n' + filter_html + '\n                <div class="flex gap-2">')

# Add TH for hewan
target_th = '''                                <th class="px-4 py-4 border-b border-slate-100">Umur</th>
                                <th class="px-4 py-4 border-b border-slate-100">Berat</th>'''
replace_th = target_th + '''
                                @if(!isset($status_laporan) || $status_laporan == 'semua')
                                    <th class="px-4 py-4 border-b border-slate-100">Status</th>
                                @elseif($status_laporan == 'ditolak')
                                    <th class="px-4 py-4 border-b border-slate-100">Keterangan</th>
                                @endif'''
content = content.replace(target_th, replace_th)

# Add TD for hewan
target_td = '''                                    <td class="px-4 py-3">{{ $row->umur ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $row->berat ?? '-' }} Kg</td>'''
replace_td = target_td + '''
                                    @if(!isset($status_laporan) || $status_laporan == 'semua')
                                        <td class="px-4 py-3">{{ $row->status }}</td>
                                    @elseif($status_laporan == 'ditolak')
                                        <td class="px-4 py-3">{{ $row->status }}</td>
                                    @endif'''
content = content.replace(target_td, replace_td)

# Fix export url
content = content.replace('url = "{{ route(\'laporan.export\') }}?jenis_laporan=" + jns + "&start_date=" + startDate + "&end_date=" + endDate + "&kategori=" + kat +', 'let statusLaporan = document.querySelector(\'select[name="status_laporan"]\').value;\n                let url = "{{ route(\'laporan.export\') }}?jenis_laporan=" + jns + "&start_date=" + startDate + "&end_date=" + endDate + "&kategori=" + kat + "&status_laporan=" + statusLaporan +')

with open(r'c:\laragon\www\rph\resources\views\laporan\index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
