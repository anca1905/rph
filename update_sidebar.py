import sys
with open(r'c:\laragon\www\rph\resources\views\layouts\app.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()
target = '''                        <a href="{{ url('/hewan') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('hewan') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-cow w-5 text-center {{ Request::is('hewan') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Data Hewan</span>
                        </a>'''
replacement = target + '''
                        <a href="{{ url('/hewan/tolak') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('hewan/tolak') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-times-circle w-5 text-center {{ Request::is('hewan/tolak') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Data Hewan Tolak</span>
                        </a>'''
content = content.replace(target, replacement)
with open(r'c:\laragon\www\rph\resources\views\layouts\app.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
