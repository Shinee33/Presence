@extends('layouts.presensi')

@section('header')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection

@section('content')
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-content">
            @if(Auth::user()->foto_profil)
                <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Profile" class="profile-avatar">
            @else
                <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="Profile" class="profile-avatar">
            @endif
            <div class="profile-name">{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</div>
            <div class="profile-role">{{ Auth::user()->jabatan ?? 'Head of IT' }}</div>
        </div>
    </div>

    <!-- Welcome Message Card -->
    <div class="menu-card text-center">
        <div class="welcome-message">
            <h4 style="margin: 0; color: #444;">Hallo My Brother and My Sister "{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}",</h4>
            <p style="margin: 0; color: #666;">Selamat Menjalani Hari Yang Indah Ini, Tetap Semangat dan Pantang Menyerah Jangan Lupa Absen Yach</p>
        </div>
    </div>

    <!-- Quick Menu (Optional - currently commented out) -->
    {{-- 
    <div class="menu-card">
        <div class="menu-grid">
            <a href="/editprofile" class="menu-item">
                <div class="menu-icon">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div class="menu-name">Profil</div>
            </a>
            
            <a href="#" class="menu-item">
                <div class="menu-icon">
                    <ion-icon name="calendar-outline"></ion-icon>
                </div>
                <div class="menu-name">Cuti</div>
            </a>
            
            <a href="/presensi/histori" class="menu-item">
                <div class="menu-icon">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <div class="menu-name">Histori</div>
            </a>
            
            <a href="#" class="menu-item">
                <div class="menu-icon">
                    <ion-icon name="location-outline"></ion-icon>
                </div>
                <div class="menu-name">Lokasi</div>
            </a>
        </div>
    </div> 
    --}}

    <!-- Presence Section -->
    <div class="presence-section">
        <!-- Today's Presence Cards -->
        <div class="row g-3 mb-4">
            @php
                $foto_in = $presensihariini && $presensihariini->foto_in ? Storage::url('uploads/absensi/' . $presensihariini->foto_in) : null;
                $foto_out = $presensihariini && $presensihariini->foto_out ? Storage::url('uploads/absensi/' . $presensihariini->foto_out) : null;
            @endphp

            <div class="col-6">
                <div class="presence-card">
                    @if ($foto_in)
                        <img src="{{ url($foto_in) }}" alt="Check In" class="presence-photo checkin">
                    @else
                        <div class="no-presence">
                            <ion-icon name="camera-outline"></ion-icon>
                        </div>
                    @endif
                    <div class="presence-title">Masuk Kerja</div>
                    <div class="presence-time">
                        {{ $presensihariini ? $presensihariini->jam_in : 'Belum Presensi' }}
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="presence-card">
                    @if ($foto_out)
                        <img src="{{ url($foto_out) }}" alt="Check Out" class="presence-photo checkout">
                    @else
                        <div class="no-presence">
                            <ion-icon name="camera-outline"></ion-icon>
                        </div>
                    @endif
                    <div class="presence-title">Pulang Kerja</div>
                    <div class="presence-time">
                        {{ $presensihariini && $presensihariini->jam_out ? $presensihariini->jam_out : 'Belum Presensi' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="section-title">Rekap Presensi {{ $namabulan[$bulanini] ?? 'Bulan Ini' }} {{ $tahunini }}</div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon success">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                </div>
                <div class="stat-number">{{ $rekappresensi['jmlhadir'] ?? 0 }}</div>
                <div class="stat-label">Hadir</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <div class="stat-number">{{ $rekapizin->jmlizin ?? 0 }}</div>
                <div class="stat-label">Izin</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger">
                    <ion-icon name="medical-outline"></ion-icon>
                </div>
                <div class="stat-number">{{ $rekapizin->jmlsakit ?? 0 }}</div>
                <div class="stat-label">Sakit</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <ion-icon name="time-outline"></ion-icon>
                </div>
                <div class="stat-number">{{ $rekappresensi['jmlalpa'] ?? 0 }}</div>
                <div class="stat-label">Alpa</div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="tabs-container">
        <div class="custom-tabs">
            <a href="#" class="custom-tab active" data-tab="bulanini">Bulan Ini</a>
            {{-- <a href="#" class="custom-tab" data-tab="leaderboard">Leaderboard</a> --}}
        </div>

        <!-- Tab Content: Current Month History -->
        <div id="bulanini" class="tab-content active">
            <div class="history-list">
                @if(isset($historibulanini) && $historibulanini->count() > 0)
                    @foreach ($historibulanini as $d)
                        <div class="history-item">
                            <div class="history-icon">
                                <ion-icon name="calendar-outline"></ion-icon>
                            </div>
                            <div class="history-content">
                                <div class="history-date">{{ date("d M Y", strtotime($d->tgl_presensi)) }}</div>
                                <div class="history-times">
                                    @if($d->jam_in)
                                        <span class="time-badge checkin">Masuk: {{ $d->jam_in }}</span>
                                    @endif
                                    @if($d->jam_out)
                                        <span class="time-badge checkout">Pulang: {{ $d->jam_out }}</span>
                                    @elseif($d->jam_in)
                                        <span class="time-badge absent">Belum Pulang</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="history-item">
                        <div class="history-icon">
                            <ion-icon name="information-circle-outline"></ion-icon>
                        </div>
                        <div class="history-content">
                            <div class="history-date">Belum Ada Data</div>
                            <div class="history-times">
                                <span class="time-badge absent">Tidak ada presensi bulan ini</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Content: Leaderboard -->
        <div id="leaderboard" class="tab-content">
            <div class="history-list">
                @if(isset($leaderboard) && count($leaderboard) > 0)
                    @foreach($leaderboard as $index => $user)
                        <div class="leaderboard-item">
                            <img src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : asset('assets/img/sample/avatar/avatar' . (($index % 5) + 1) . '.jpg') }}" 
                                 alt="{{ $user->nama_lengkap }}" class="leaderboard-avatar">
                            <div class="leaderboard-content">
                                <div class="leaderboard-name">{{ $user->nama_lengkap ?? $user->name }}</div>
                                <div class="leaderboard-position">{{ $user->jabatan ?? 'Karyawan' }}</div>
                                <div class="leaderboard-stats">{{ $user->total_hadir ?? 0 }} hari hadir</div>
                            </div>
                            <div class="leaderboard-rank">#{{ $index + 1 }}</div>
                        </div>
                    @endforeach
                @else
                    {{-- Default static leaderboard if no data --}}
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="leaderboard-item">
                            <img src="{{ asset('assets/img/sample/avatar/avatar' . $i . '.jpg') }}" 
                                 alt="User {{ $i }}" class="leaderboard-avatar">
                            <div class="leaderboard-content">
                                <div class="leaderboard-name">Karyawan {{ $i }}</div>
                                <div class="leaderboard-position">{{ ['Senior Developer', 'UI/UX Designer', 'Project Manager', 'Backend Developer', 'Frontend Developer'][$i-1] }}</div>
                                <div class="leaderboard-stats">{{ 20 - $i }} hari hadir</div>
                            </div>
                            <div class="leaderboard-rank">#{{ $i }}</div>
                        </div>
                    @endfor
                @endif
            </div>
        </div>
    </div>

    <!-- Floating Action Button (Optional) -->
    {{-- 
    <a href="/presensi" class="floating-button">
        <ion-icon name="camera-outline"></ion-icon>
    </a>
    --}}

    <!-- Bottom spacing for mobile -->
    <div style="height: 100px;"></div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush