import sys

with open(r'c:\laragon\www\rph\app\Http\Controllers\HewanController.php', 'r', encoding='utf-8') as f:
    content = f.read()

target = '''    public function index()
    {
        $hewans = Hewan::orderBy('tanggal_masuk', 'desc')->get();

        $lastPH = Hewan::where('no_registrasi', 'like', 'PH-%')->orderBy('id_hewan', 'desc')->first();
        $nextPH = $lastPH ? 'PH-' . str_pad(intval(substr($lastPH->no_registrasi, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'PH-001';

        return view('hewan.index', compact('hewans', 'nextPH'));
    }'''

replacement = '''    public function index()
    {
        $hewans = Hewan::where('status', 'not like', '%Ditolak%')->orderBy('tanggal_masuk', 'desc')->get();

        $lastPH = Hewan::where('no_registrasi', 'like', 'PH-%')->orderBy('id_hewan', 'desc')->first();
        $nextPH = $lastPH ? 'PH-' . str_pad(intval(substr($lastPH->no_registrasi, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'PH-001';

        return view('hewan.index', compact('hewans', 'nextPH'));
    }

    public function tolak()
    {
        $hewans = Hewan::where('status', 'like', '%Ditolak%')->orderBy('tanggal_masuk', 'desc')->get();

        $lastPH = Hewan::where('no_registrasi', 'like', 'PH-%')->orderBy('id_hewan', 'desc')->first();
        $nextPH = $lastPH ? 'PH-' . str_pad(intval(substr($lastPH->no_registrasi, 3)) + 1, 3, '0', STR_PAD_LEFT) : 'PH-001';

        return view('hewan.tolak', compact('hewans', 'nextPH'));
    }'''

content = content.replace(target, replacement)

with open(r'c:\laragon\www\rph\app\Http\Controllers\HewanController.php', 'w', encoding='utf-8') as f:
    f.write(content)
