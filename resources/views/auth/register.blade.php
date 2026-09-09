<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi | RPH Kolaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body
    class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex items-center justify-center relative py-8 px-4">

    <div
        class="absolute top-0 left-0 w-full h-[40vh] bg-emerald-700 rounded-b-[50px] lg:rounded-b-[100px] z-0 shadow-lg">
    </div>

    <div class="relative z-10 w-full max-w-md my-auto">

        <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-slate-100">

            <div class="flex justify-center mb-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md shadow-emerald-500/30">
                        R
                    </div>
                    <span class="text-xl font-extrabold text-slate-800 tracking-tight">RPH Kolaka</span>
                </div>
            </div>

            <div class="text-center mb-5">
                <h3 class="text-lg font-bold text-slate-800">Buat Akun Baru</h3>
                <p class="text-xs text-slate-500 mt-1">Daftar sebagai petugas operasional.</p>
            </div>

            @if ($errors->any())
                <div
                    class="bg-red-50 text-red-600 border border-red-100 px-4 py-2 rounded-xl mb-5 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <p class="text-xs font-semibold">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-user text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Masukkan nama lengkap"
                            class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-slate-50 focus:bg-white text-sm font-medium text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-400 text-sm"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="example@gmail.com"
                            class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-slate-50 focus:bg-white text-sm font-medium text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Buat Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 text-sm"></i>
                        </div>
                        <input type="password" name="password" id="reg_password" required
                            placeholder="Minimal 8 karakter"
                            class="w-full pl-9 pr-10 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-slate-50 focus:bg-white text-sm font-medium text-slate-700">
                        <button type="button" onclick="togglePass('reg_password', 'eye1')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-600 focus:outline-none">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 text-sm"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="reg_password_conf" required
                            placeholder="Ketik ulang password"
                            class="w-full pl-9 pr-10 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-slate-50 focus:bg-white text-sm font-medium text-slate-700">
                        <button type="button" onclick="togglePass('reg_password_conf', 'eye2')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-600 focus:outline-none">
                            <i class="fas fa-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded-xl hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 active:scale-[0.98] mt-2">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-xs text-slate-600 font-medium">Sudah punya akun?
                    <a href="{{ route('login') }}"
                        class="text-emerald-600 font-bold hover:text-emerald-700 transition-colors">Masuk di sini</a>
                </p>
            </div>
        </div>

        <div class="mt-6 text-center relative z-10 pb-4">
            <p class="text-[10px] text-slate-500 font-medium tracking-wide">
                &copy; {{ date('Y') }} DINAS PERKEBUNAN DAN PETERNAKAN KAB. KOLAKA
            </p>
        </div>
    </div>

    <script>
        function togglePass(inputId, iconId) {
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
    </script>
</body>

</html>
