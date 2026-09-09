<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | RPH Kolaka</title>
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
                        class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md">
                        R</div>
                    <span class="text-xl font-extrabold text-slate-800 tracking-tight">RPH Kolaka</span>
                </div>
            </div>
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Lupa Password?</h3>
                <p class="text-xs text-slate-500 mt-1">Masukkan email terdaftar Anda. Kami akan mengirimkan 6 digit kode
                    OTP.</p>
            </div>
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 px-4 py-2 rounded-xl mb-5 text-xs font-semibold"><i
                        class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}</div>
            @endif
            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"><i
                                class="fas fa-envelope text-slate-400 text-sm"></i></div>
                        <input type="email" name="email" required placeholder="example@gmail.com"
                            class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-sm font-medium">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded-xl hover:bg-emerald-700 transition-all mt-2">Kirim
                    Kode OTP</button>
            </form>
            <div class="mt-5 text-center"><a href="{{ route('login') }}"
                    class="text-xs text-emerald-600 font-bold hover:text-emerald-700"><i
                        class="fas fa-arrow-left mr-1"></i> Kembali ke Login</a></div>
        </div>
    </div>
</body>

</html>
