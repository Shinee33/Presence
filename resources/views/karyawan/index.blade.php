@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl ps-4">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Data Karyawan</h2>
            </div>
            <div class="col-12 col-md-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-tambah-karyawan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Karyawan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
<div class="container-xl ps-4">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                </svg>
            </div>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if(session('error'))
<div class="container-xl ps-4">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

<!-- Page Body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Karyawan</h3>
                        <div class="card-actions">
                            <form action="{{ route('panel.karyawan.index') }}" method="GET" class="d-flex gap-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Cari karyawan..." id="search-karyawan" value="{{ request('search') }}">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <circle cx="10" cy="10" r="7"/>
                                            <line x1="21" y1="21" x2="15" y2="15"/>
                                        </svg>
                                    </span>
                                </div>
                                <select name="kode_dept" id="kode_dept" class="form-select" style="min-width: 200px;" onchange="this.form.submit()">
                                    <option value="">Semua Departemen</option>
                                    @if(isset($departemen))
                                        @foreach($departemen as $dept)
                                            <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                                                {{ $dept->nama_dept }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>No. HP</th>
                                        <th>Foto</th>
                                        <th>Departemen</th>
                                        <th class="w-1">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($karyawan as $d)
                                    <tr>
                                        <td>{{ $loop->iteration + (($karyawan->currentPage() ?? 1) - 1) * ($karyawan->perPage() ?? 10) }}</td>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">{{ $d->nama_lengkap }}</div>
                                                    @if(isset($d->email) && $d->email)
                                                        <div class="text-muted">{{ $d->email }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $d->jabatan }}</td>
                                        <td>{{ $d->no_hp }}</td>
                                        <td>
                                            @if($d->foto_profil)
                                                <span class="avatar avatar-sm" style="background-image: url('{{ asset('storage/uploads/foto/' . $d->foto_profil) }}')">
                                                    <img src="{{ asset('storage/uploads/foto/' . $d->foto_profil) }}" 
                                                         alt="Foto {{ $d->nama_lengkap }}" 
                                                         class="rounded"
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         onerror="this.style.display='none'; this.parentNode.innerHTML='<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'icon avatar-img\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\' fill=\'none\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path stroke=\'none\' d=\'M0 0h24v24H0z\' fill=\'none\'/><circle cx=\'12\' cy=\'7\' r=\'4\'/><path d=\'m6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2\'/></svg>';">
                                                </span>
                                            @else
                                                <span class="avatar avatar-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon avatar-img" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <circle cx="12" cy="7" r="4"/>
                                                        <path d="m6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($d->departemen)
                                                <span class="badge bg-secondary-lt">{{ $d->departemen }}</span>
                                                @if($d->kode_dept)
                                                    <div class="text-muted small">{{ $d->kode_dept }}</div>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <!-- Tombol Edit -->
                                                <a href="{{ route('panel.karyawan.edit', $d->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                        <path d="M16 5l3 3"/>
                                                    </svg>
                                                </a>
                                                
                                                <!-- Tombol Delete -->
                                                <form action="{{ route('panel.karyawan.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan {{ $d->nama_lengkap }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <line x1="4" y1="7" x2="20" y2="7"/>
                                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <circle cx="12" cy="7" r="4"/>
                                                        <path d="m6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                                    </svg>
                                                </div>
                                                <p class="empty-title">Tidak ada data karyawan</p>
                                                <p class="empty-subtitle text-muted">
                                                    @if(request('search'))
                                                        Tidak ditemukan karyawan dengan kata kunci "{{ request('search') }}"
                                                    @else
                                                        Belum ada karyawan yang terdaftar di sistem
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($karyawan, 'hasPages') && $karyawan->hasPages())
                            <div class="card-footer d-flex align-items-center">
                                {{ $karyawan->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->


@push('scripts')

@endsection