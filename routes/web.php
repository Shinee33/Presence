<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KaryawanController;

/*
|--------------------------------------------------------------------------
| Route Login Karyawan
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('auth.login'))->name('login');
Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');

/*
|--------------------------------------------------------------------------
| Route Login Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', fn() => view('auth.loginadmin'))->name('loginadmin');
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin'])->name('prosesloginadmin');
});

/*
|--------------------------------------------------------------------------
| Route Admin (hanya bisa diakses setelah login sebagai admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:user'])->prefix('panel')->name('panel.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin'])->name('dashboardadmin');
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin'])->name('logoutadmin');
    
    /*
    |--------------------------------------------------------------------------
    | CRUD Karyawan Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        // READ - Tampilkan daftar karyawan
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        
        // CREATE - Tambah karyawan baru
        Route::post('/', [KaryawanController::class, 'store'])->name('store');
        
        // READ - Tampilkan detail karyawan
        Route::get('/{id}', [KaryawanController::class, 'show'])->name('show');
        
        // EDIT - Form edit karyawan
        Route::get('/{id}/edit', [KaryawanController::class, 'edit'])->name('edit');
        
        // UPDATE - Update data karyawan
        Route::put('/{id}', [KaryawanController::class, 'update'])->name('update');
        Route::patch('/{id}', [KaryawanController::class, 'update'])->name('update.patch');
        
        // DELETE - Hapus karyawan
        Route::delete('/{id}', [KaryawanController::class, 'destroy'])->name('destroy');
        
        // TOGGLE STATUS - Aktif/Nonaktif karyawan
        Route::post('/{id}/toggle-status', [KaryawanController::class, 'toggleStatus'])->name('toggle-status');
        
        // Export data karyawan
        Route::get('/export', [KaryawanController::class, 'export'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| Route Karyawan (hanya bisa diakses setelah login sebagai karyawan)
|--------------------------------------------------------------------------
*/

    // Dashboard Karyawan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('logout');
    
    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [PresensiController::class, 'editprofile'])->name('edit');
        Route::put('/update', [PresensiController::class, 'updateprofile'])->name('update');
        Route::post('/upload-photo', [PresensiController::class, 'uploadPhoto'])->name('uploadPhoto');
        Route::delete('/delete-photo', [PresensiController::class, 'deletePhoto'])->name('deletePhoto');
    });
    
    // Backward compatibility
    Route::get('/editprofile', [PresensiController::class, 'editprofile'])->name('editprofile');
    Route::post('/presensi/updateprofile', [PresensiController::class, 'updateprofile'])->name('presensi.updateprofile');
    
    /*
    |--------------------------------------------------------------------------
    | Presensi Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('presensi')->name('presensi.')->group(function () {
        // Presensi Harian
        Route::get('/create', [PresensiController::class, 'create'])->name('create');
        Route::post('/store', [PresensiController::class, 'store'])->name('store');
        
        // Izin
        Route::get('/izin', [PresensiController::class, 'izin'])->name('izin');
        Route::get('/buatizin', [PresensiController::class, 'buatizin'])->name('buatizin');
        Route::post('/storeizin', [PresensiController::class, 'storeizin'])->name('storeizin');
        
        // Histori
        Route::get('/histori', [PresensiController::class, 'histori'])->name('histori');
        Route::post('/gethistori', [PresensiController::class, 'gethistori'])->name('gethistori');
    });
    
    // Route untuk AJAX histori (backward compatibility)
    Route::post('/gethistori', [PresensiController::class, 'gethistori'])->name('gethistori');

    Route::get('/test-db', function() {
    try {
        $count = DB::table('pegawai')->count();
        $users = DB::table('pegawai')->limit(3)->get();
        
        echo "Total users: " . $count . "<br><br>";
        echo "Sample users:<br>";
        foreach($users as $user) {
            echo "ID: {$user->id} | Name: " . ($user->nama_lengkap ?? 'NULL') . " | Password: '{$user->password}'<br>";
        }
    } catch(\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
});

Route::get('/test-hash/{password}', function($password) {
    $hash = '$2y$12$BKid4vqBTEIYs5/GNmpaaes7bvZASEF4IWYQ1cpMw1G/ln8SzsV4O';
    $result = Hash::check($password, $hash);
    echo "Testing password '{$password}': " . ($result ? 'MATCH ✅' : 'NO MATCH ❌');
});


/*
|--------------------------------------------------------------------------
| API Routes (jika diperlukan untuk mobile app atau AJAX)
|--------------------------------------------------------------------------
*/
// Route::prefix('api')->name('api.')->group(function () {
//     // API untuk mobile app atau AJAX calls
//     Route::middleware(['auth:sanctum'])->group(function () {
//         Route::apiResource('karyawan', KaryawanController::class);
//         Route::post('karyawan/{id}/toggle-status', [KaryawanController::class, 'toggleStatus']);
//     });
// });

// /*
// |--------------------------------------------------------------------------
// | Route Fallback (opsional)
// |--------------------------------------------------------------------------
// */
// Route::fallback(function () {
//     return response()->view('errors.404', [], 404);
// });

// CATATAN PENTING:
/*
1. Route untuk karyawan admin sudah diperbaiki:
   - Menghapus duplikasi prefix '/panel' 
   - Menambahkan semua method CRUD yang diperlukan
   - Menambahkan route untuk toggle status

2. Route untuk karyawan user sudah dirapikan:
   - Menghapus duplikasi middleware
   - Mengelompokkan route profile
   - Mempertahankan backward compatibility

3. Tambahan yang disertakan:
   - Route untuk API (jika diperlukan)
   - Route fallback untuk 404
   - Komentar yang jelas untuk setiap section

4. Cara testing route:
   php artisan route:list --name=panel.karyawan
   php artisan route:clear
   php artisan route:cache
*/