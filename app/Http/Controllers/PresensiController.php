<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
// use Intervention\Image\Facades\Image; // Commented out - tidak digunakan
use App\Models\Izin;

class PresensiController extends Controller
{
    public function index()
    {
        // Ambil data karyawan berdasarkan user yang login
        $user_id = Auth::guard('karyawan')->user()->user_id;
        $karyawan = DB::table('pegawai')->where('user_id', $user_id)->first();
        
        return view('profile.index', compact('karyawan'));
    }

    public function create()
    {
        $hariini = date("Y-m-d");
        $user_id = Auth::guard('karyawan')->user()->id;

        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('user_id', $user_id)
            ->count();

        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        try {
            if (!$request->has('image') || !$request->image) {
                echo "error|Gambar tidak ditemukan dalam request|";
                return;
            }

            if (!$request->has('lokasi') || !str_contains($request->lokasi, ',')) {
                echo "error|Lokasi tidak valid|";
                return;
            }

            $user = Auth::guard('karyawan')->user();
            $user_id = $user->id;
            $nama_lengkap = $user->nama_lengkap;
            $tgl_presensi = date("Y-m-d");
            $jam = date("H:i:s");

            $latitudekantor = -1.6341801515074925;
            $longitudekantor = 103.54967400825598;

            $lokasi = $request->lokasi;
            $lokasiuser = explode(",", $lokasi);
            $latitudeuser = $lokasiuser[0];
            $longitudeuser = $lokasiuser[1];

            $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
            $radius = round($jarak["meters"]);

            if ($radius > 10000) {
                echo "error|Kejauhan Es, Dekatin Lagi Dari Kantor Baru Absen Ya|";
                return;
            }

            $cek = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('user_id', $user_id)
                ->count();

            $ket = $cek > 0 ? "out" : "in";

            $image = $request->image;
            $formatName = $nama_lengkap . "-" . $tgl_presensi . "-" . $ket;
            $image_parts = explode(";base64,", $image);

            if (count($image_parts) > 1) {
                $image_base64 = base64_decode($image_parts[1]);
            } else {
                echo "error|Gagal decode gambar|";
                return;
            }

            $fileName = $formatName . ".png";
            Storage::disk('public')->put("uploads/absensi/$fileName", $image_base64);

            if ($cek > 0) {
                $data_pulang = [
                    'jam_out'    => $jam,
                    'foto_out'   => $fileName,
                    'lokasi_out' => $lokasi,
                ];

                $update = DB::table('presensi')
                    ->where('tgl_presensi', $tgl_presensi)
                    ->where('user_id', $user_id)
                    ->update($data_pulang);

                if ($update) {
                    echo "success|Selamat Pulang, Hati-hati di Jalan dan jangan lupa bersyukur.😘|out";
                } else {
                    echo "error|Ulang lagi ya, Gagal Presensi Pulang Nich|out";
                }
            } else {
                $data = [
                    'user_id'      => $user_id,
                    'nama_lengkap' => $nama_lengkap,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in'       => $jam,
                    'foto_in'      => $fileName,
                    'lokasi_in'    => $lokasi,
                ];

                $simpan = DB::table('presensi')->insert($data);

                if ($simpan) {
                    echo "success|Semoga Hari Ini Dapat Hal Baik, Naik Gaji 💲 Misalnya 🥹🫶|in";
                } else {
                    echo "error|Ulang lagi ya, Gagal Presensi Masuk Nich|in";
                }
            }
        } catch (\Exception $e) {
            Log::error('Presensi gagal: ' . $e->getMessage());
            echo "error|Terjadi kesalahan server: " . $e->getMessage() . "|";
        }
    }

    public function editprofile()
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::guard('karyawan')->user();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request)
    {
        /** @var \App\Models\Karyawan $karyawan */
        $karyawan = Auth::guard('karyawan')->user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
        ]);

        /** @var \App\Models\Karyawan $karyawan */
        $karyawan->update([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
        ]);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui');
    }

    public function uploadPhoto(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('Upload photo request received', [
                'user_id' => Auth::id(),
                'files' => $request->hasFile('foto_profil'),
                'file_size' => $request->hasFile('foto_profil') ? $request->file('foto_profil')->getSize() : 'no file'
            ]);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'foto_profil' => [
                    'required',
                    'image',
                    'mimes:jpeg,jpg,png',
                    'max:2048' // 2MB dalam kilobytes
                ]
            ], [
                'foto_profil.required' => 'Foto profil harus dipilih',
                'foto_profil.image' => 'File harus berupa gambar',
                'foto_profil.mimes' => 'Format file harus JPG, JPEG, atau PNG',
                'foto_profil.max' => 'Ukuran file maksimal 2MB'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::guard('karyawan')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $file = $request->file('foto_profil');
            
            // Generate nama file unik
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Buat folder jika belum ada
            $uploadPath = 'public/profile_photos';
            if (!Storage::exists($uploadPath)) {
                Storage::makeDirectory($uploadPath);
            }

            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                Storage::delete('public/' . $user->foto_profil);
                Log::info('Old profile photo deleted: ' . $user->foto_profil);
            }

            // Path lengkap untuk menyimpan file
            $filePath = 'profile_photos/' . $fileName;
            
            // Upload file langsung tanpa processing
            $file->storeAs('public/profile_photos', $fileName);
            Log::info('Image uploaded successfully without processing');

            // Update database menggunakan DB query builder
            DB::table('pegawai')
                ->where('id', $user->id)
                ->update(['foto_profil' => $filePath, 'updated_at' => now()]);

            Log::info('Profile photo updated successfully', [
                'user_id' => $user->id,
                'file_path' => $filePath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'photo_url' => asset('storage/' . $filePath)
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading profile photo: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePhoto(Request $request)
    {
        try {
            $user = Auth::guard('karyawan')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            if ($user->foto_profil) {
                // Hapus file dari storage
                if (Storage::exists('public/' . $user->foto_profil)) {
                    Storage::delete('public/' . $user->foto_profil);
                }

                // Update database menggunakan DB query builder
                DB::table('pegawai')
                    ->where('id', $user->id)
                    ->update(['foto_profil' => null, 'updated_at' => now()]);

                Log::info('Profile photo deleted successfully', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil dihapus'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada foto profil untuk dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting profile photo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto'
            ], 500);
        }
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $user_id = Auth::guard('karyawan')->user()->id;

        $histori = DB::table('presensi')
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun)
            ->where('user_id', $user_id)
            ->orderBy('tgl_presensi', 'asc')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function izin() 
    {   
        try {
            $user = Auth::guard('karyawan')->user();
            
            // Debug user data
            if (!$user) {
                Log::error('User not authenticated in izin method');
                abort(403, 'Unauthorized');
            }

            $dataizin = DB::table('pengajuan_izin')
                ->where('user_id', $user->id)
                ->orderBy('tgl_izin', 'desc')
                ->get();

            // Debug query results
            Log::info('Data Izin Count: ' . $dataizin->count());
            Log::info('Data Izin: ' . json_encode($dataizin));

            return view('presensi.izin', compact('dataizin'));

        } catch (\Exception $e) {
            Log::error('Error in izin method: ' . $e->getMessage());
            return view('presensi.izin')->with('error', 'Gagal memuat data izin');
        }
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        try {
            // Debug: Log semua data yang diterima
            Log::info('=== FORM IZIN SUBMISSION ===');
            Log::info('Request Method: ' . $request->method());
            Log::info('Request Data: ', $request->all());
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tgl_izin' => 'required|date|after_or_equal:today',
                'status' => 'required|in:i,s',
                'keterangan' => 'required|string|min:10|max:500'
            ], [
                'tgl_izin.required' => 'Tanggal izin harus diisi',
                'tgl_izin.date' => 'Format tanggal tidak valid',
                'tgl_izin.after_or_equal' => 'Tanggal izin tidak boleh kurang dari hari ini',
                'status.required' => 'Jenis izin harus dipilih',
                'status.in' => 'Jenis izin tidak valid',
                'keterangan.required' => 'Keterangan harus diisi',
                'keterangan.min' => 'Keterangan minimal 10 karakter',
                'keterangan.max' => 'Keterangan maksimal 500 karakter'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed: ', $validator->errors()->toArray());
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $user = Auth::guard('karyawan')->user();
            
            if (!$user) {
                Log::error('User not authenticated');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }
            
            // Debug: Log user data
            Log::info('User data: ', [
                'id' => $user->id, 
                'nama' => $user->nama_lengkap ?? 'NULL'
            ]);
            
            // Cek duplikasi izin
            $existingIzin = DB::table('pengajuan_izin')
                ->where('user_id', $user->id)
                ->where('tgl_izin', $request->tgl_izin)
                ->where('status', $request->status)
                ->whereIn('status_approved', [0, 1])
                ->first();

            if ($existingIzin) {
                Log::warning('Duplicate izin found: ', (array)$existingIzin);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah mengajukan izin ' . ($request->status == 'i' ? 'Izin' : 'Sakit') . ' pada tanggal tersebut'
                ], 400);
            }

            // Prepare data
            $data = [
                'user_id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap ?? 'Unknown',
                'tgl_izin' => $request->tgl_izin,
                'status' => $request->status,
                'keterangan' => trim($request->keterangan),
                'status_approved' => 0, // 0 = pending
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Debug: Log data yang akan disimpan
            Log::info('Data to insert: ', $data);

            // Insert data
            $inserted = DB::table('pengajuan_izin')->insert($data);

            if ($inserted) {
                Log::info('Data berhasil disimpan ke database');
                
                // Verify insertion
                $lastInserted = DB::table('pengajuan_izin')
                    ->where('user_id', $user->id)
                    ->where('tgl_izin', $request->tgl_izin)
                    ->where('status', $request->status)
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                Log::info('Last inserted record: ', (array)$lastInserted);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Permohonan izin berhasil dikirim dan menunggu persetujuan',
                    'redirect' => url('/dashboard')
                ], 200);
            } else {
                Log::error('Gagal menyimpan ke database - insert returned false');
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data ke database'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in storeizin: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) +
            (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}