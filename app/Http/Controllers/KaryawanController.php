<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Karyawan;



class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan filter
        $search = $request->get('search');
        $kode_dept = $request->get('kode_dept');

        // Query dasar dengan join
        $query = DB::table('pegawai')
            ->leftJoin('departemen', 'pegawai.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'pegawai.id',
                'pegawai.nama_lengkap', 
                'pegawai.jabatan', 
                'pegawai.no_hp', 
                'pegawai.email',
                'pegawai.foto_profil', 
                'pegawai.kode_dept',
                'departemen.nama_dept as departemen'
            );

        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('pegawai.nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('pegawai.jabatan', 'LIKE', "%{$search}%")
                  ->orWhere('pegawai.no_hp', 'LIKE', "%{$search}%");
            });
        }

        // Filter departemen
        if ($kode_dept) {
            $query->where('pegawai.kode_dept', $kode_dept);
        }

        // Ambil data dengan pagination
        $karyawan = $query->orderBy('pegawai.nama_lengkap', 'asc')
                         ->paginate(10);

        // Ambil data departemen untuk dropdown
        $departemen = DB::table('departemen')->get();

        return view('karyawan.index', compact('karyawan', 'departemen'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi input - PERBAIKAN: Hapus validasi 'id' karena auto increment
            $request->validate([
                'nama' => 'required|string|max:255', // Sesuai dengan nama field di form
                'jabatan' => 'required|string|max:100',
                'no_hp' => 'required|string|max:20',
                'password' => 'required|min:6',
                'departemen' => 'required|exists:departemen,kode_dept', // Pastikan departemen ada
                'email' => 'nullable|email|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' // Sesuai dengan nama field di form
            ], [
                'nama.required' => 'Nama lengkap wajib diisi',
                'jabatan.required' => 'Jabatan wajib diisi',
                'no_hp.required' => 'Nomor HP wajib diisi',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter',
                'departemen.required' => 'Departemen wajib dipilih',
                'departemen.exists' => 'Departemen tidak valid',
                'foto.image' => 'File harus berupa gambar',
                'foto.max' => 'Ukuran file maksimal 2MB'
            ]);

            // Siapkan data - PERBAIKAN: Sesuaikan nama field
            $data = [
                'nama_lengkap' => $request->nama, // Form menggunakan 'nama', tabel menggunakan 'nama_lengkap'
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'kode_dept' => $request->departemen,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Proses upload foto jika ada - PERBAIKAN: Gunakan nama field yang benar
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                
                // Buat nama file unik
                $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                
                // Simpan file
                $path = $foto->storeAs('uploads/foto', $filename, 'public');
                
                if ($path) {
                    $data['foto_profil'] = $filename;
                }
            }

            // Simpan ke database - PERBAIKAN: Tambah pengecekan
            $result = DB::table('pegawai')->insert($data);

            if ($result) {
                return redirect()->route('panel.karyawan.index')
                               ->with('success', 'Karyawan berhasil ditambahkan!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Gagal menambahkan karyawan. Silakan coba lagi.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput()
                           ->with('error', 'Data tidak valid. Periksa kembali form Anda.');
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error creating employee: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $karyawan = DB::table('pegawai')
            ->leftJoin('departemen', 'pegawai.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'pegawai.*', 
                'departemen.nama_dept as departemen'
            )
            ->where('pegawai.id', $id)
            ->first();

        if (!$karyawan) {
            return redirect()->route('panel.karyawan.index')
                           ->with('error', 'Karyawan tidak ditemukan.');
        }

        return view('karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = DB::table('pegawai')->where('id', $id)->first();
        
        if (!$karyawan) {
            return redirect()->route('panel.karyawan.index')
                           ->with('error', 'Karyawan tidak ditemukan.');
        }

        $departemen = DB::table('departemen')->get();

        return view('karyawan.edit', compact('karyawan', 'departemen'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Cek apakah karyawan ada
            $karyawan = DB::table('pegawai')->where('id', $id)->first();
            if (!$karyawan) {
                return redirect()->route('panel.karyawan.index')
                               ->with('error', 'Karyawan tidak ditemukan.');
            }

            // Validasi input
            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'jabatan' => 'required|string|max:100',
                'no_hp' => 'required|string|max:20',
                'departemen' => 'required|exists:departemen,kode_dept',
                'email' => 'nullable|email|max:255',
                'password' => 'nullable|min:6',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            // Siapkan data untuk update
            $data = [
                'nama_lengkap' => $request->nama_lengkap,
                'jabatan' => $request->jabatan,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
                'kode_dept' => $request->departemen,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Proses upload foto baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($karyawan->foto_profil) {
                    Storage::disk('public')->delete('uploads/foto/' . $karyawan->foto_profil);
                }

                $foto = $request->file('foto');
                $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('uploads/foto', $filename, 'public');
                $data['foto_profil'] = $filename;
            }

            // Update data
            DB::table('pegawai')->where('id', $id)->update($data);

            return redirect()->route('panel.karyawan.index')
                           ->with('success', 'Data karyawan berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = DB::table('pegawai')->where('id', $id)->first();
            
            if (!$karyawan) {
                return redirect()->route('panel.karyawan.index')
                               ->with('error', 'Karyawan tidak ditemukan.');
            }

            // Hapus foto jika ada
            if ($karyawan->foto_profil) {
                Storage::disk('public')->delete('uploads/foto/' . $karyawan->foto_profil);
            }

            // Hapus data dari database
            DB::table('pegawai')->where('id', $id)->delete();

            return redirect()->route('panel.karyawan.index')
                           ->with('success', 'Karyawan berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $karyawan = DB::table('pegawai')->where('id', $id)->first();
            
            if (!$karyawan) {
                return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
            }

            // Toggle status (asumsi ada field 'status')
            $newStatus = $karyawan->status == 'aktif' ? 'nonaktif' : 'aktif';
            
            DB::table('pegawai')->where('id', $id)->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Status berhasil diubah',
                'status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }
}