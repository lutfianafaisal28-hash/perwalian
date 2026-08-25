<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;

// ============================================================
// CONTROLLER KELOLA DATA DOSEN (Sisi Admin)
// ============================================================
// Menangani CRUD data dosen. Sama seperti MahasiswaController:
// setiap dosen otomatis mendapat akun login dengan username = NIDN.
// ============================================================
class DosenController extends Controller
{
    // ===== DAFTAR DOSEN (GET /admin/dosen) =====
    public function index(Request $request)
    {
        $query = Dosen::query();

        // Fitur pencarian berdasarkan NIDN atau nama
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nidn', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%");
            });
        }

        // withCount('mahasiswaBimbingan') = tambahkan kolom hitungan
        // jumlah mahasiswa yang dibimbing dosen ini (tanpa query tambahan)
        $dosen = $query->withCount('mahasiswaBimbingan')->latest()->paginate(6)->withQueryString();

        return view('admin.dosen.index', compact('dosen'));
    }

    // ===== FORM TAMBAH DOSEN (GET /admin/dosen/create) =====
    public function create()
    {
        return view('admin.dosen.create');
    }

    // ===== SIMPAN DOSEN BARU (POST /admin/dosen) =====
    public function store(Request $request)
    {
        // Validasi. nidn harus unik di tabel dosen.
        $data = $request->validate([
            'nidn' => ['required', 'string', 'max:20', 'unique:dosen,nidn'],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $password = $data['password'] ?: '123456';

        // 1) Buat akun login: username = NIDN, role = dosen
        $user = User::create([
            'name' => $data['nama'],
            'username' => $data['nidn'],
            'email' => $data['email'] ?: null,
            'role' => User::ROLE_DOSEN,
            'password' => $password,
        ]);

        // 2) Buat data dosen yang menautkan ke akun user
        Dosen::create([
            'user_id' => $user->id,
            'nidn' => $data['nidn'],
            'nama' => $data['nama'],
        ]);

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan. Password default: '.$password);
    }

    // ===== DETAIL DOSEN (GET /admin/dosen/{dosen}) =====
    public function show(Dosen $dosen)
    {
        // Ambil daftar mahasiswa bimbingan dosen ini (relasi dari Dosen),
        // lengkap dengan jumlah catatan perwalian masing-masing.
        $mahasiswa = $dosen->mahasiswaBimbingan()
            ->withCount('perwalian')
            ->orderBy('nama')
            ->get();

        return view('admin.dosen.show', compact('dosen', 'mahasiswa'));
    }

    // ===== FORM EDIT DOSEN (GET /admin/dosen/{dosen}/edit) =====
    public function edit(Dosen $dosen)
    {
        return view('admin.dosen.edit', compact('dosen'));
    }

    // ===== PERBARUI DOSEN (PUT /admin/dosen/{dosen}) =====
    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
            'nidn' => ['required', 'string', 'max:20', 'unique:dosen,nidn,'.$dosen->id],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email,'.$dosen->user_id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $dosen->update([
            'nidn' => $data['nidn'],
            'nama' => $data['nama'],
        ]);

        $dosen->user?->update([
            'name' => $data['nama'],
            'username' => $data['nidn'],
            'email' => $data['email'] ?: null,
            'password' => filled($data['password']) ? $data['password'] : $dosen->user->password,
        ]);

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    // ===== HAPUS DOSEN (DELETE /admin/dosen/{dosen}) =====
    public function destroy(Dosen $dosen)
    {
        $user = $dosen->user;

        $dosen->delete();

        $user?->delete();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
