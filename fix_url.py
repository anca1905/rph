import sys
with open(r'c:\laragon\www\rph\resources\views\laporan\index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('let let statusLaporan', 'let statusLaporan')

with open(r'c:\laragon\www\rph\resources\views\laporan\index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
