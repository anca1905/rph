import sys
import os

# 1. Remove from sidebar
with open(r'c:\laragon\www\rph\resources\views\layouts\app.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

target_sidebar = '''
                        <a href="{{ url('/hewan/tolak') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('hewan/tolak') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-times-circle w-5 text-center {{ Request::is('hewan/tolak') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Data Hewan Tolak</span>
                        </a>'''
content = content.replace(target_sidebar, '')

with open(r'c:\laragon\www\rph\resources\views\layouts\app.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

# 2. Remove route
with open(r'c:\laragon\www\rph\routes\web.php', 'r', encoding='utf-8') as f:
    route_content = f.read()
route_content = route_content.replace("        Route::get('/hewan/tolak', [HewanController::class, 'tolak'])->name('hewan.tolak');\n", "")
with open(r'c:\laragon\www\rph\routes\web.php', 'w', encoding='utf-8') as f:
    f.write(route_content)

# 3. Remove tolak view
tolak_view = r'c:\laragon\www\rph\resources\views\hewan\tolak.blade.php'
if os.path.exists(tolak_view):
    os.remove(tolak_view)
