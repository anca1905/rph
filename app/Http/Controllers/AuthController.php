<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\User; // Pastikan Model User di-import

class AuthController extends Controller
{
    /**
     * ==========================================
     * BAGIAN 1: LOGIN & LOGOUT
     * ==========================================
     */

    // Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses validasi dan pengecekan login
    public function login(Request $request)
    {
        // Validasi inputan form
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Cek apakah user menceklis "Ingat Saya"
        $remember = $request->has('remember');

        // Mencoba login (Mencocokkan inputan email ke kolom 'email' atau 'username' di Database)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember) || 
            Auth::attempt(['username' => $request->email, 'password' => $request->password], $remember)) {
            
            // Jika berhasil, buat sesi baru dan arahkan ke Dashboard
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Proses Keluar (Logout)
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Hapus semua sesi untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Arahkan kembali ke halaman form login (halaman utama)
        return redirect('/');
    }


    /**
     * ==========================================
     * BAGIAN 2: REGISTRASI PENGGUNA BARU
     * ==========================================
     */

    // Menampilkan halaman form registrasi
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses penyimpanan data pengguna baru
    public function register(Request $request)
    {
        // Validasi inputan form
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // Memastikan password sama dengan password_confirmation
        ]);

        // Buat user baru di database
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        
        // Atur role otomatis menjadi 'petugas'
        // CATATAN: Pastikan di database tabel users Anda memiliki kolom 'role'
        $user->role = 'petugas'; 
        
        $user->save();

        // Setelah sukses mendaftar, langsung login-kan otomatis
        Auth::login($user);

        // Arahkan ke dashboard dengan pesan sukses
        return redirect()->intended('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang, Petugas.');
    }


    /**
     * ==========================================
     * BAGIAN 3: PENGATURAN PROFIL & PASSWORD
     * ==========================================
     */

    // Memperbarui Nama dan Email Profil melalui Modal Pop-up
    public function updateProfile(Request $request)
    {
        // Validasi inputan profil
        $request->validate([
            'name'  => 'required|string|max:255',
            // Pastikan email unik, tapi abaikan pengecekan jika itu adalah email milik user ini sendiri
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    // Memperbarui Password melalui Modal Pop-up
    public function updatePassword(Request $request)
    {
        // Validasi inputan password
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed', 
        ]);

        $user = Auth::user();

        // Cek apakah password lama yang dimasukkan sesuai dengan yang ada di database
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        // Jika cocok, simpan password baru yang sudah di-enkripsi (Hash)
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password Anda berhasil diubah!');
    }
    /**
     * ==========================================
     * BAGIAN 4: LUPA PASSWORD & OTP EMAIL
     * ==========================================
     */

    // 1. Tampilkan form input email
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Generate OTP dan Kirim ke Email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.'
        ]);

        $otp = rand(100000, 999999); // Generate 6 digit angka

        // Simpan OTP ke tabel password_reset_tokens bawaan Laravel
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $otp, 'created_at' => Carbon::now()]
        );

        // Kirim Email dengan template HTML yang cantik
        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)->subject('Kode OTP Reset Password - RPH Kolaka');
        });

        // Simpan email di session untuk halaman verifikasi
        session(['reset_email' => $request->email]);

        return redirect()->route('password.verify')->with('success', 'Kode OTP telah dikirim ke email Anda!');
    }

    // 3. Tampilkan form input OTP
    public function showVerifyForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi berakhir, silakan masukkan email kembali.']);
        }
        return view('auth.verify-otp');
    }

    // 4. Verifikasi kecocokan OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);
        $email = session('reset_email');

        $resetRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$resetRecord || $resetRecord->token !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.']);
        }

        // Cek kadaluarsa (misal 15 menit)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')->withErrors(['email' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.']);
        }

        // OTP Benar, izinkan masuk ke halaman reset password
        session(['otp_verified' => true]);
        return redirect()->route('password.reset');
    }

    // 5. Tampilkan form password baru
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }

    // 6. Simpan password baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();
        
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token & bersihkan session
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru Anda.');
    }
}