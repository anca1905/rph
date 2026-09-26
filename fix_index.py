import sys
import re

with open(r'c:\laragon\www\rph\resources\views\laporan\index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Revert grid
content = content.replace('grid-cols-1 md:grid-cols-5', 'grid-cols-1 md:grid-cols-4')

# Remove status_laporan filter
target_filter = r'''                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Status Laporan (Khusus Hewan)</label>
                    <select name="status_laporan".*?</select>\n                </div>'''
content = re.sub(target_filter, '', content, flags=re.DOTALL)

# Add hewan_ditolak to select
target_hewan = '''<option value="hewan" {{ isset($jenis_laporan) && $jenis_laporan == 'hewan' ? 'selected' : '' }}>
                            Data Hewan (Reguler)</option>'''
replace_hewan = target_hewan + '''
                        <option value="hewan_ditolak" {{ isset($jenis_laporan) && $jenis_laporan == 'hewan_ditolak' ? 'selected' : '' }}>
                            Data Hewan Ditolak</option>'''
content = content.replace(target_hewan, replace_hewan)

# Adjust TH
target_th = '''                                @if(!isset($status_laporan) || $status_laporan == 'semua')
                                    <th class="px-4 py-4 border-b border-slate-100">Status</th>
                                @elseif($status_laporan == 'ditolak')
                                    <th class="px-4 py-4 border-b border-slate-100">Keterangan</th>
                                @endif'''
content = content.replace(target_th, '')

# Add logic for both hewan and hewan_ditolak
content = content.replace("@if ($jenis_laporan == 'hewan')", "@if (in_array($jenis_laporan, ['hewan', 'hewan_ditolak']))")

target_th_2 = '''                                <th class="px-4 py-4 border-b border-slate-100">Berat</th>'''
replace_th_2 = target_th_2 + '''
                                @if($jenis_laporan == 'hewan_ditolak')
                                    <th class="px-4 py-4 border-b border-slate-100">Keterangan</th>
                                @endif'''
content = content.replace(target_th_2, replace_th_2)


# Adjust TD
target_td = '''                                    @if(!isset($status_laporan) || $status_laporan == 'semua')
                                        <td class="px-4 py-3">{{ $row->status }}</td>
                                    @elseif($status_laporan == 'ditolak')
                                        <td class="px-4 py-3">{{ $row->status }}</td>
                                    @endif'''
content = content.replace(target_td, '')

target_td_2 = '''                                    <td class="px-4 py-3 font-semibold">{{ $row->berat ?? '-' }} Kg</td>'''
replace_td_2 = target_td_2 + '''
                                    @if($jenis_laporan == 'hewan_ditolak')
                                        <td class="px-4 py-3">{{ $row->status }}</td>
                                    @endif'''
content = content.replace(target_td_2, replace_td_2)


# Fix URL
content = content.replace('let statusLaporan = document.querySelector(\'select[name="status_laporan"]\').value;\n                let url = "{{ route(\'laporan.export\') }}?jenis_laporan=" + jns + "&start_date=" + startDate + "&end_date=" + endDate + "&kategori=" + kat + "&status_laporan=" + statusLaporan +', 'let url = "{{ route(\'laporan.export\') }}?jenis_laporan=" + jns + "&start_date=" + startDate + "&end_date=" + endDate + "&kategori=" + kat +')


with open(r'c:\laragon\www\rph\resources\views\laporan\index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
