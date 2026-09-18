<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP Reset Password</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #059669; padding: 25px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px; }
        .content { padding: 30px; color: #4a5568; line-height: 1.6; font-size: 15px; }
        .otp-box { background-color: #f0fdf4; border: 2px dashed #34d399; padding: 15px; text-align: center; font-size: 36px; font-weight: bold; color: #047857; letter-spacing: 8px; margin: 25px 0; border-radius: 8px; }
        .warning { font-size: 13px; color: #e53e3e; background-color: #fff5f5; padding: 10px; border-left: 4px solid #e53e3e; margin-bottom: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #718096; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RPH KOLAKA</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mengatur ulang kata sandi (reset password) akun Anda di <strong>Sistem Informasi Operasional RPH Kabupaten Kolaka</strong>.</p>
            <p>Berikut adalah kode OTP Anda. Silakan masukkan kode ini pada form verifikasi:</p>
            
            <div class="otp-box">
                {{ $otp }}
            </div>
            
            <div class="warning">
                <strong>PENTING:</strong> Kode ini bersifat rahasia dan hanya berlaku selama <strong>15 menit</strong>. Jangan pernah memberikan kode ini kepada siapapun!
            </div>
            
            <p>Jika Anda tidak merasa melakukan permintaan reset kata sandi ini, Anda dapat mengabaikan email ini. Akun Anda akan tetap aman.</p>
            <p style="margin-top: 30px;">Terima kasih,<br><strong>Admin RPH Kolaka</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Dinas Perkebunan dan Peternakan Kabupaten Kolaka.<br>Semua Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>
