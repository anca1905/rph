import sys

# PemotonganController
with open(r'c:\laragon\www\rph\app\Http\Controllers\PemotonganController.php', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("Hewan::where('kategori', 'Hewan Harian')", "Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%')")
with open(r'c:\laragon\www\rph\app\Http\Controllers\PemotonganController.php', 'w', encoding='utf-8') as f:
    f.write(content)

# PostmortemController
with open(r'c:\laragon\www\rph\app\Http\Controllers\PostmortemController.php', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("Hewan::where('kategori', 'Hewan Harian')", "Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%')")
with open(r'c:\laragon\www\rph\app\Http\Controllers\PostmortemController.php', 'w', encoding='utf-8') as f:
    f.write(content)

# AntemortemController
with open(r'c:\laragon\www\rph\app\Http\Controllers\AntemortemController.php', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("Hewan::where('kategori', 'Hewan Harian')", "Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%')")
with open(r'c:\laragon\www\rph\app\Http\Controllers\AntemortemController.php', 'w', encoding='utf-8') as f:
    f.write(content)

# PembayaranController
with open(r'c:\laragon\www\rph\app\Http\Controllers\PembayaranController.php', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("Hewan::where('kategori', 'Hewan Harian')", "Hewan::where('kategori', 'Hewan Harian')->where('status', 'not like', '%Ditolak%')")
with open(r'c:\laragon\www\rph\app\Http\Controllers\PembayaranController.php', 'w', encoding='utf-8') as f:
    f.write(content)

