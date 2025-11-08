@extends('layouts.presensi')

@section('header')
<div class="appHeader text-light" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack" style="color: white; opacity: 0.9;">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle" style="font-weight: 600; letter-spacing: 0.5px;">Histori</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top:70px">
    <div class="col">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="">Pilih Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>
                                {{ $namabulan[$i] }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="">Pilih Tahun</option>
                        @php
                            $tahunmulai = 2022;
                            $tahunskrg = date("Y");
                        @endphp
                        @for ($tahun = $tahunmulai; $tahun <= $tahunskrg; $tahun++)
                            <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-primary btn-block" id="getdata">
                        <i class="fa fa-search"></i> Cari Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col" id="showhistori">
        <div class="alert alert-info text-center">
            <i class="fa fa-info-circle"></i> Silakan pilih bulan dan tahun terlebih dahulu.
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $("#getdata").click(function(e) {
            e.preventDefault();
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();

            // Validasi input
            if (bulan === "" || tahun === "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Tampilkan loading
            $("#showhistori").html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data histori...</p>
                </div>
            `);

            $.ajax({
                type: 'POST',
                url: '/gethistori',
                data: {
                    _token: "{{ csrf_token() }}",
                    bulan: bulan,
                    tahun: tahun,
                },
                timeout: 15000, // 15 detik timeout
                success: function(response) {
                    console.log('Response berhasil diterima');
                    $("#showhistori").html(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    
                    let errorMessage = 'Gagal mengambil data histori.';
                    
                    if (xhr.status === 404) {
                        errorMessage = 'Route tidak ditemukan. Hubungi administrator.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                    } else if (xhr.status === 419) {
                        errorMessage = 'Session expired. Silakan refresh halaman dan coba lagi.';
                    } else if (status === 'timeout') {
                        errorMessage = 'Request timeout. Silakan coba lagi.';
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak ada koneksi ke server. Periksa koneksi internet Anda.';
                    }
                    
                    $("#showhistori").html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> 
                            ${errorMessage}
                            <br><small>Error Code: ${xhr.status} | Status: ${status}</small>
                            <br><button class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">Refresh Halaman</button>
                        </div>
                    `);
                }
            });
        });
    });
</script>
@endpush