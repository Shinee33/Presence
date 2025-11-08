@extends('layouts.presensi')

@section('header')
    <link rel="stylesheet" href="{{ asset('assets/css/izin.css') }}">
    <div class="appHeader text-light" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Izin</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="container-izin">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Izin Card List --}}
        <div class="izin-list">
            @forelse ($dataizin as $d)
                <div class="izin-card">
                    <div class="izin-header">
                        <span class="izin-type {{ $d->status == 'S' ? 'sakit' : 'izin' }}">
                            {{ $d->status == 'S' ? 'Sakit' : ($d->status == 'I' ? 'Izin' : 'Telah Pengajuan') }}
                        </span>
                        <span class="izin-date">
                            {{ \Carbon\Carbon::parse($d->tgl_izin)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <div class="izin-body">
                        <p class="izin-desc">{{ $d->keterangan ?? '-' }}</p>
                    </div>
                    <div class="izin-footer">
                        @if ($d->status_approved == 0)
                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                        @elseif ($d->status_approved == 1)
                            <span class="badge bg-success">Disetujui</span>
                        @elseif ($d->status_approved == 2)
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <ion-icon name="file-tray-outline"></ion-icon>
                    <h5>Selamat Preseni Kamu Masih Bersih</h5>
                    <p>Kamu belum pernah mengajukan izin atau sakit nich teruskan yachhhh jangan malas-malas kerjanya.💯</p>
                    <a href="/presensi/buatizin" class="btn-ajukan">
                        <ion-icon name="add-circle-outline"></ion-icon> Ajukan Sekarang
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Floating Button --}}
        <a href="/presensi/buatizin" class="fab-button">
            <ion-icon name="add-outline"></ion-icon>
        </a>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/izin.js') }}"></script>
@endpush
