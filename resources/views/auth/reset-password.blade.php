<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru | RPH Kolaka</title>
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
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Buat Password Baru</h3>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan password baru yang kuat dan mudah diingat.</p>
            </div>
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 px-4 py-2 rounded-xl mb-5 text-xs font-semibold">
                    {{ $errors->first() }}</div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" required placeholder="Ketik ulang password"
                        class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-sm">
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded-xl hover:bg-emerald-700 transition-all mt-2">Simpan
                    Password Baru</button>
            </form>
        </div>
    </div>
</body>

</html>
