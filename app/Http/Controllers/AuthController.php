<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function proseslogin(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'password' => 'required'
        ], [
            'id.required' => 'ID harus diisi',
            'password.required' => 'Password harus diisi'
        ]);

        // Cari user di database
        $user = DB::table('pegawai')
            ->where('id', $request->id)
            ->first();

        if ($user) {
            // PENTING: Gunakan Hash::check() untuk password yang di-bcrypt
            if (Hash::check($request->password, $user->password)) {
                // Login berhasil - gunakan loginUsingId
                Auth::guard('karyawan')->loginUsingId($user->id);
                
                // Regenerate session untuk keamanan
                $request->session()->regenerate();
                
                return redirect('/dashboard')->with('success', 'Login berhasil');
            }
        }

        // Login gagal
        return back()->with('warning', 'ID atau Password salah')->withInput($request->only('id'));
    }

    // ALTERNATIF: Menggunakan Auth::attempt() jika sudah ada Model
    public function prosesloginWithModel(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'id' => $request->id,
            'password' => $request->password
        ];

        // Gunakan attempt() - Laravel akan otomatis handle Hash::check()
        if (Auth::guard('karyawan')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard')->with('success', 'Login berhasil');
        }

        return back()->with('warning', 'ID atau Password salah')->withInput($request->only('id'));
    }

    public function proseslogout(Request $request)
    {
        Auth::guard('karyawan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Logout berhasil');
    }

    // Login Admin (jika berbeda)
    public function prosesloginadmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::guard('user')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/panel/dashboardadmin')->with('success', 'Login admin berhasil');
        }

        return back()->with('warning', 'Email atau Password salah')->withInput($request->only('email'));
    }

    public function proseslogoutadmin(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/panel')->with('success', 'Logout admin berhasil');
    }
}