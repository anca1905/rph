import sys

# AntemortemController
with open(r'c:\laragon\www\rph\app\Http\Controllers\AntemortemController.php', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%')", "Hewan::where('kategori', 'Hewan Harian')")
with open(r'c:\laragon\www\rph\app\Http\Controllers\AntemortemController.php', 'w', encoding='utf-8') as f:
    f.write(content)

# PembayaranController
with open(r'c:\laragon\www\rph\app\Http\Controllers\PembayaranController.php', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%')", "Hewan::where('kategori', 'Hewan Harian')")
with open(r'c:\laragon\www\rph\app\Http\Controllers\PembayaranController.php', 'w', encoding='utf-8') as f:
    f.write(content)

