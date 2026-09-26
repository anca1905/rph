<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Operasional RPH Kolaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #065f46;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #047857;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden">

    @if (session('success'))
        <div id="toast-success"
            class="fixed top-5 right-5 z-[100] bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 transition-opacity duration-500">
            <i class="fas fa-check-circle text-lg"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
            <button onclick="document.getElementById('toast-success').remove()"
                class="ml-4 text-emerald-500 hover:text-emerald-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if ($errors->any())
        <div id="toast-error"
            class="fixed top-5 right-5 z-[100] bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 transition-opacity duration-500">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span class="font-bold text-sm">{{ $errors->first() }}</span>
            <button onclick="document.getElementById('toast-error').remove()"
                class="ml-4 text-red-500 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="flex h-screen w-full relative">

        <aside id="sidebar"
            class="sidebar-scroll flex flex-col w-64 h-screen px-4 py-5 overflow-y-auto bg-gradient-to-b from-emerald-800 to-green-900 shadow-[4px_0_24px_rgba(0,0,0,0.06)] shrink-0 z-40 relative transition-all duration-300">
            <div class="flex items-center gap-3 mb-5 px-2 cursor-pointer transition-transform hover:scale-[1.02]">
                <img src="{{ asset('Lambang_Kab_Kolaka.png') }}" alt="Logo Kolaka" 
                    class="w-10 h-10 object-contain drop-shadow-md">
                <h2 class="text-xl font-bold text-white tracking-wide">RPH <span
                        class="text-green-400 font-medium">Kolaka</span></h2>
            </div>

            <div class="flex flex-col justify-between flex-1">
                <nav class="space-y-1">
                    <p class="text-[10px] font-bold text-emerald-300/70 uppercase tracking-widest mb-1.5 px-3">Main Menu
                    </p>

                    @php
                        if (auth()->check() && in_array(auth()->user()->role, ['petugas', 'admin', 'pimpinan'])) {
                            $pendingPembayaran = \App\Models\Hewan::where('kategori', 'Hewan Harian')
                                ->where(function($q) {
                                    $q->doesntHave('pembayaran')
                                      ->orWhereHas('pembayaran', function($q2) {
                                          $q2->where('status_pembayaran', '!=', 'Lunas');
                                      });
                                })->count();

                            $pendingAM = \App\Models\Hewan::where('kategori', 'Hewan Harian')
                                ->doesntHave('antemortem')->count();

                            $pendingPotong = \App\Models\Hewan::where('kategori', 'Hewan Harian')
                                ->whereHas('pembayaran', function($q) {
                                    $q->where('status_pembayaran', 'Lunas');
                                })
                                ->whereHas('antemortem', function($q) {
                                    $q->where('status_antemortem', 'Lolos');
                                })
                                ->where(function($q) {
                                    $q->doesntHave('pemotongan')
                                      ->orWhereHas('pemotongan', function($q2) {
                                          $q2->where('status_pemotongan', '!=', 'Selesai Dipotong');
                                      });
                                })->count();

                            $pendingPM = \App\Models\Hewan::where('kategori', 'Hewan Harian')
                                ->whereHas('pemotongan', function($q) {
                                    $q->where('status_pemotongan', 'Selesai Dipotong');
                                })
                                ->doesntHave('postmortem')->count();
                        } else {
                            $pendingPembayaran = $pendingAM = $pendingPotong = $pendingPM = 0;
                        }
                    @endphp

                    <a href="{{ url('/dashboard') }}"
                        class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('/dashboard') || Request::is('dashboard') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                        <i
                            class="fas fa-th-large w-5 text-center {{ Request::is('/dashboard') || Request::is('dashboard') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                        <span class="mx-3 text-sm">Dashboard</span>
                    </a>

                    @if (auth()->check() && in_array(auth()->user()->role, ['petugas', 'admin', 'pimpinan']))
                        <a href="{{ url('/hewan') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('hewan') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-cow w-5 text-center {{ Request::is('hewan') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Data Hewan</span>
                        </a>
                        <a href="{{ url('/hewan/tolak') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('hewan/tolak') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-times-circle w-5 text-center {{ Request::is('hewan/tolak') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Data Hewan Tolak</span>
                        </a>
                    @endif

                    @if (auth()->check() && in_array(auth()->user()->role, ['petugas', 'admin', 'pimpinan', 'pekerja_idul_adha']))
                        <a href="{{ url('/idul_adha') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('idul_adha') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-calendar-alt w-5 text-center {{ Request::is('idul_adha') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Data Idul Adha</span>
                        </a>
                    @endif

                    @if (auth()->check() && in_array(auth()->user()->role, ['petugas', 'admin', 'pimpinan']))
                        <a href="{{ url('/pembayaran') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('pembayaran') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-wallet w-5 text-center {{ Request::is('pembayaran') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm flex-1">Data Pembayaran</span>
                            @if($pendingPembayaran > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">{{ $pendingPembayaran }}</span>
                            @endif
                        </a>

                        <div class="py-1">
                            <hr class="border-emerald-700/50">
                        </div>
                        <p class="text-[10px] font-bold text-emerald-300/70 uppercase tracking-widest mb-1.5 px-3">
                            Proses Operasional</p>

                        <a href="{{ url('/antemortem') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('antemortem') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-stethoscope w-5 text-center {{ Request::is('antemortem') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm flex-1">Antemortem</span>
                            @if($pendingAM > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">{{ $pendingAM }}</span>
                            @endif
                        </a>
                        <a href="{{ url('/pemotongan') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('hasil_potong') || Request::is('pemotongan') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-drumstick-bite w-5 text-center {{ Request::is('hasil_potong') || Request::is('pemotongan') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm flex-1">Pemotongan</span>
                            @if($pendingPotong > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">{{ $pendingPotong }}</span>
                            @endif
                        </a>
                        <a href="{{ url('/postmortem') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('postmortem') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-microscope w-5 text-center {{ Request::is('postmortem') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm flex-1">Postmortem</span>
                            @if($pendingPM > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">{{ $pendingPM }}</span>
                            @endif
                        </a>
                    @endif

                    @if (auth()->check() && in_array(auth()->user()->role, ['pimpinan', 'admin']))
                        <div class="py-1">
                            <hr class="border-emerald-700/50">
                        </div>
                        <p class="text-[10px] font-bold text-emerald-300/70 uppercase tracking-widest mb-1.5 px-3">
                            Rekapitulasi</p>

                        <a href="{{ url('/laporan') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('laporan') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-file-alt w-5 text-center {{ Request::is('laporan') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Laporan</span>
                        </a>
                    @endif

                    @if (auth()->check() && auth()->user()->role === 'admin')
                        <div class="py-1">
                            <hr class="border-emerald-700/50">
                        </div>
                        <p class="text-[10px] font-bold text-emerald-300/70 uppercase tracking-widest mb-1.5 px-3">
                            Manajemen</p>

                        <a href="{{ url('/users') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('users') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i class="fas fa-user-shield w-5 text-center {{ Request::is('users') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Kelola Akun</span>
                        </a>

                        <a href="{{ url('/pekerja') }}"
                            class="group flex items-center px-3 py-2 rounded-xl transition-all duration-300 {{ Request::is('pekerja') ? 'bg-white text-emerald-800 shadow-md font-bold translate-x-1' : 'text-emerald-100 hover:bg-white/10 hover:text-white font-medium' }}">
                            <i
                                class="fas fa-users-cog w-5 text-center {{ Request::is('pekerja') ? 'text-green-500' : 'text-emerald-400 group-hover:text-green-300' }} transition-colors"></i>
                            <span class="mx-3 text-sm">Kelola Pekerja</span>
                        </a>
                    @endif

                </nav>
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-screen overflow-hidden">

            <header
                class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 px-8 py-4 flex items-center justify-between shrink-0 shadow-sm z-30 sticky top-0">

                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()"
                        class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-emerald-100 text-slate-500 hover:text-emerald-600 rounded-xl transition-colors focus:outline-none shadow-sm">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Sistem Informasi Operasional RPH Kolaka</p>
                    </div>
                </div>

                <div onclick="toggleProfilePanel()"
                    class="flex items-center gap-3 cursor-pointer hover:bg-white bg-slate-100/50 border border-slate-200 py-1.5 pl-4 pr-1.5 rounded-full transition-all duration-300 shadow-sm hover:shadow-md">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-700 leading-none">
                            {{ auth()->user()->name ?? 'Pengguna' }}</p>
                        <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-1">
                            {{ auth()->check() ? strtoupper(auth()->user()->role) : 'SISTEM RPH' }}
                        </p>
                    </div>
                    <div
                        class="w-9 h-9 bg-gradient-to-br from-emerald-100 to-green-200 border border-emerald-300 rounded-full flex items-center justify-center text-emerald-700 font-bold shadow-inner">
                        <i class="fas fa-user-shield text-sm"></i>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </div>
        </main>

        <div id="profileBackdrop" onclick="toggleProfilePanel()"
            class="fixed inset-0 bg-slate-900/40 z-40 hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm">
        </div>

        <div id="profilePanel"
            class="fixed inset-y-0 right-0 w-80 max-w-full bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col border-l border-slate-100">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-slate-800">Pengaturan Akun</h3>
                <button onclick="toggleProfilePanel()"
                    class="text-slate-400 hover:text-red-500 hover:rotate-90 transition-all duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 flex flex-col items-center border-b border-slate-100">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-green-200 border-4 border-white shadow-md rounded-full flex items-center justify-center text-emerald-700 font-bold mb-3 relative">
                    <i class="fas fa-user-shield text-3xl"></i>
                    <span
                        class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <h2 class="text-lg font-bold text-slate-800">{{ auth()->user()->name ?? 'Pengguna' }}</h2>
                <p class="text-xs text-green-600 font-semibold uppercase tracking-wider mt-1">
                    {{ auth()->check() ? auth()->user()->role : 'Akses Terbatas' }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ auth()->user()->email ?? 'email@rphkolaka.com' }}</p>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-1">
                <button onclick="openModal('modalEditProfil'); toggleProfilePanel();"
                    class="w-full flex items-center px-4 py-3 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors font-medium text-sm">
                    <i class="fas fa-user-edit w-6 text-center"></i>
                    <span class="ml-2">Edit Profil</span>
                </button>
                <button onclick="openModal('modalUbahPassword'); toggleProfilePanel();"
                    class="w-full flex items-center px-4 py-3 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors font-medium text-sm">
                    <i class="fas fa-key w-6 text-center"></i>
                    <span class="ml-2">Ubah Password</span>
                </button>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-2.5 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 hover:text-red-700 transition-colors text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditProfil"
        class="fixed inset-0 z-[60] hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl relative mx-4">
            <button onclick="closeModal('modalEditProfil')"
                class="absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-lg font-bold text-slate-800 mb-1"><i class="fas fa-user-edit text-emerald-600 mr-2"></i>
                Edit Profil</h3>
            <p class="text-xs text-slate-500 mb-5">Sesuaikan nama lengkap dan email akun Anda.</p>

            <form action="{{ route('profil.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                            required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                            required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modalEditProfil')"
                        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors shadow-sm">Simpan
                        Profil</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalUbahPassword"
        class="fixed inset-0 z-[60] hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl relative mx-4">
            <button onclick="closeModal('modalUbahPassword')"
                class="absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-lg font-bold text-slate-800 mb-1"><i class="fas fa-key text-emerald-600 mr-2"></i> Ubah
                Password</h3>
            <p class="text-xs text-slate-500 mb-5">Ketikkan password lama Anda sebagai bentuk verifikasi keamanan, lalu
                buat password baru.</p>

            <form action="{{ route('profil.password') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Ketik Password Lama</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                placeholder="Ketik password saat ini..."
                                class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
                            <button type="button" onclick="toggleModalPassword('current_password', 'eye_current')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-600 transition-colors focus:outline-none">
                                <i class="fas fa-eye" id="eye_current"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Buat Password Baru</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password" required
                                placeholder="Buat password baru..."
                                class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
                            <button type="button" onclick="toggleModalPassword('new_password', 'eye_new')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-600 transition-colors focus:outline-none">
                                <i class="fas fa-eye" id="eye_new"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                required placeholder="Ketik ulang password baru..."
                                class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-emerald-500">
                            <button type="button"
                                onclick="toggleModalPassword('new_password_confirmation', 'eye_confirm')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-600 transition-colors focus:outline-none">
                                <i class="fas fa-eye" id="eye_confirm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modalUbahPassword')"
                        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors shadow-sm">Ubah
                        Password</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // SCRIPT BARU UNTUK TOGGLE SIDEBAR KE KIRI
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            // Menambahkan/menghapus margin kiri minus untuk menyembunyikan sidebar
            sidebar.classList.toggle('-ml-64');
        }

        // Script untuk Profile Panel & Modal Lainnya
        function toggleProfilePanel() {
            const panel = document.getElementById('profilePanel');
            const backdrop = document.getElementById('profileBackdrop');
            if (panel.classList.contains('translate-x-full')) {
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
                panel.classList.remove('translate-x-full');
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            }
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function toggleModalPassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        setTimeout(() => {
            const successToast = document.getElementById('toast-success');
            const errorToast = document.getElementById('toast-error');
            if (successToast) {
                successToast.classList.add('opacity-0');
                setTimeout(() => successToast.remove(), 500);
            }
            if (errorToast) {
                errorToast.classList.add('opacity-0');
                setTimeout(() => errorToast.remove(), 500);
            }
        }, 5000);
    </script>
    @yield('scripts')
</body>

</html>
