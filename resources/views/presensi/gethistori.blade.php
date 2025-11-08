@if (!isset($histori))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> 
        Data histori tidak dapat dimuat. Terjadi kesalahan pada server.
    </div>
@elseif ($histori->isEmpty())
    <div class="alert alert-warning text-center">
        <i class="fa fa-info-circle fa-2x mb-2"></i>
        <h6>Data Tidak Ditemukan</h6>
        <p class="mb-0">Data presensi tidak ditemukan untuk periode yang dipilih.</p>
        <small class="text-muted">Pastikan Anda sudah melakukan presensi pada bulan dan tahun tersebut.</small>
    </div>
@else
    @foreach ($histori as $d)
        <div class="card mb-3" style="border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border: none;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0 text-dark font-weight-bold">
                        <i class="fa fa-calendar text-primary mr-2"></i> 
                        {{ date("d F Y", strtotime($d->tgl_presensi)) }}
                    </h6>
                    @if ($d->jam_in)
                        <span class="badge {{ $d->jam_in <= '07:00:00' ? 'badge-success' : 'badge-warning' }} px-2 py-1">
                            {{ $d->jam_in <= '07:00:00' ? 'Tepat Waktu' : 'Terlambat' }}
                        </span>
                    @endif
                </div>
                
                <div class="row">
                    <!-- Foto dan Jam Masuk -->
                    <div class="col-6">
                        <div class="text-center">
                            <h6 class="text-success mb-2 font-weight-bold">
                                <i class="fa fa-sign-in-alt mr-1"></i> Masuk
                            </h6>
                            @if($d->foto_in)
                                @php
                                    $path_in = Storage::url('uploads/absensi/' . $d->foto_in);
                                @endphp
                                <div class="position-relative">
                                    <img src="{{ url($path_in) }}" 
                                         alt="Foto Masuk" 
                                         class="img-fluid rounded shadow-sm mb-2" 
                                         style="max-height: 120px; width: 100%; object-fit: cover; cursor: pointer;"
                                         onclick="showImageModal('{{ url($path_in) }}', 'Foto Masuk - {{ date("d/m/Y", strtotime($d->tgl_presensi)) }}')">
                                    <div class="position-absolute" style="top: 5px; right: 5px;">
                                        <span class="badge badge-dark badge-sm">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-center border rounded mb-2" 
                                     style="height: 120px; background-color: #f8f9fa;">
                                    <div class="text-muted text-center">
                                        <i class="fa fa-image fa-2x mb-2"></i>
                                        <p class="mb-0 small">Tidak ada foto</p>
                                    </div>
                                </div>
                            @endif
                            <div class="mt-2">
                                @if($d->jam_in)
                                    <span class="badge {{ $d->jam_in <= '07:00:00' ? 'badge-success' : 'badge-warning' }} px-2 py-1">
                                        <i class="fa fa-clock mr-1"></i>{{ date('H:i', strtotime($d->jam_in)) }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Belum Absen</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Foto dan Jam Keluar -->
                    <div class="col-6">
                        <div class="text-center">
                            <h6 class="text-primary mb-2 font-weight-bold">
                                <i class="fa fa-sign-out-alt mr-1"></i> Keluar
                            </h6>
                            @if($d->foto_out)
                                @php
                                    $path_out = Storage::url('uploads/absensi/' . $d->foto_out);
                                @endphp
                                <div class="position-relative">
                                    <img src="{{ url($path_out) }}" 
                                         alt="Foto Keluar" 
                                         class="img-fluid rounded shadow-sm mb-2" 
                                         style="max-height: 120px; width: 100%; object-fit: cover; cursor: pointer;"
                                         onclick="showImageModal('{{ url($path_out) }}', 'Foto Keluar - {{ date("d/m/Y", strtotime($d->tgl_presensi)) }}')">
                                    <div class="position-absolute" style="top: 5px; right: 5px;">
                                        <span class="badge badge-dark badge-sm">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-center border rounded mb-2" 
                                     style="height: 120px; background-color: #f8f9fa;">
                                    <div class="text-muted text-center">
                                        <i class="fa fa-clock fa-2x mb-2"></i>
                                        <p class="mb-0 small">Belum Absen Keluar</p>
                                    </div>
                                </div>
                            @endif
                            <div class="mt-2">
                                @if($d->jam_out)
                                    <span class="badge badge-primary px-2 py-1">
                                        <i class="fa fa-clock mr-1"></i>{{ date('H:i', strtotime($d->jam_out)) }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Tambahan -->
                <hr class="my-3">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center text-small">
                            <div class="text-muted">
                                <i class="fa fa-business-time mr-1"></i> 
                                <strong>Jam Kerja:</strong> {{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '-' }} - {{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : 'Belum absen keluar' }}
                            </div>
                            @if($d->jam_in && $d->jam_out)
                                @php
                                    try {
                                        $jam_masuk = \Carbon\Carbon::createFromFormat('H:i:s', $d->jam_in);
                                        $jam_keluar = \Carbon\Carbon::createFromFormat('H:i:s', $d->jam_out);
                                        $total_jam = $jam_keluar->diff($jam_masuk);
                                        $total_menit = ($total_jam->h * 60) + $total_jam->i;
                                    } catch (\Exception $e) {
                                        $total_jam = null;
                                        $total_menit = 0;
                                    }
                                @endphp
                                @if($total_jam)
                                    <div class="text-info">
                                        <i class="fa fa-hourglass-half mr-1"></i> 
                                        <strong>Total:</strong> 
                                        @if($total_jam->h > 0)
                                            {{ $total_jam->h }}j 
                                        @endif
                                        {{ $total_jam->i }}m
                                        <small class="text-muted">({{ $total_menit }} menit)</small>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Ringkasan Data -->
    <div class="card mt-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="card-body text-white text-center">
            <h6 class="mb-2"><i class="fa fa-chart-bar mr-2"></i>Ringkasan Periode</h6>
            <div class="row">
                <div class="col-4">
                    <h5 class="mb-0">{{ $histori->count() }}</h5>
                    <small>Total Hari</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0">{{ $histori->where('jam_in', '<=', '07:00:00')->count() }}</h5>
                    <small>Tepat Waktu</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0">{{ $histori->where('jam_in', '>', '07:00:00')->count() }}</h5>
                    <small>Terlambat</small>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Modal untuk menampilkan gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Presensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" class="img-fluid w-100" alt="Foto Presensi" style="max-height: 80vh; object-fit: contain;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showImageModal(src, title) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModalLabel').textContent = title;
    $('#imageModal').modal('show');
}

// Handle error gambar
$(document).ready(function() {
    $('img').on('error', function() {
        $(this).attr('src', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjRjhGOUZBIi8+CjxwYXRoIGQ9Ik02MCA0NUw3NSA2MEg0NUw2MCA0NVoiIGZpbGw9IiM2QjczODAiLz4KPHBhdGggZD0iTTQ1IDc1SDc1VjkwSDQ1Vjc1WiIgZmlsbD0iIzZCNzM4MCIvPgo8L3N2Zz4K');
        $(this).parent().append('<small class="text-danger">Gambar tidak dapat dimuat</small>');
    });
});
</script>