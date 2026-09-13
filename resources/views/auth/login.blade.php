<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RPH Kolaka</title>
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
                <div class="flex flex-col items-center gap-3">
                    <img src="{{ asset('Lambang_Kab_Kolaka.png') }}" alt="Logo Kolaka" class="w-20 h-20 object-contain drop-shadow-lg">
                    <span class="text-2xl font-extrabold text-slate-800 tracking-tight">RPH Kolaka</span>
                </div>
            </div>

            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Selamat Datang</h3>
            </div>

            @if ($errors->any())
                <div
                    class="bg-red-50 text-red-600 border border-red-100 px-4 py-2 rounded-xl mb-5 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <p class="text-xs font-semibold">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf

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
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 text-sm"></i>
                        </div>

                        <input type="password" name="password" id="password" required placeholder="••••••••"
                            class="w-full pl-9 pr-10 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all bg-slate-50 focus:bg-white text-sm font-medium text-slate-700">

                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-600 transition-colors focus:outline-none">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-1">
                    <a href="{{ route('password.request') }}"
                        class="text-xs text-emerald-600 font-bold hover:text-emerald-700 transition-colors">Lupa
                        Password?</a>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded-xl hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 active:scale-[0.98] mt-2">
                    Masuk Sekarang
                </button>
            </form>

            {{-- <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-600 font-medium">Belum punya akun petugas?</p>
                <a href="{{ route('register') }}"
                    class="inline-block mt-1.5 text-sm text-emerald-600 font-bold hover:text-emerald-700 transition-colors underline underline-offset-4 decoration-2 decoration-emerald-600/30 hover:decoration-emerald-600">
                    Daftar Sekarang
                </a>
            </div> --}}

        </div>

        <div class="mt-6 text-center relative z-10 pb-4">
            <p class="text-[10px] text-slate-500 font-medium tracking-wide">
                &copy; {{ date('Y') }} DINAS PERKEBUNAN DAN PETERNAKAN KAB. KOLAKA
            </p>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>
