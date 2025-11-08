@extends('layouts.presensi')

@section('header')
<div class="appHeader text-light" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack" style="color: white; opacity: 0.9;">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Profil</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section" style="margin-top: 2rem; padding-bottom: 120px;">
    <div class="profile-container">
        <!-- Profile Photo Section -->
        <div class="profile-photo-section">
            <div class="profile-photo-wrapper">
                <div class="profile-photo-placeholder" id="profilePhotoContainer">
                    @if($karyawan->foto_profil)
                        <img src="{{ asset('storage/' . $karyawan->foto_profil) }}" 
                             alt="Profile Photo" 
                             class="profile-photo-img"
                             id="profileImage">
                    @else
                        <ion-icon name="person-outline" id="profileIcon"></ion-icon>
                    @endif
                </div>
                <button type="button" class="change-photo-btn" onclick="openPhotoModal()">
                    <ion-icon name="camera-outline"></ion-icon>
                    <span>Ganti Foto Profil</span>
                </button>
            </div>
            <h3 class="profile-name">{{ $karyawan->nama_lengkap }}</h3>
        </div>

        <!-- Profile Info Cards -->
        <div class="profile-info">
            <div class="info-card">
                <div class="info-card-header">
                    <ion-icon name="person-circle-outline" class="info-icon"></ion-icon>
                    <h4>Data Diri</h4>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <span class="info-label">Nama Lengkap:</span>
                        <span class="info-value">{{ $karyawan->nama_lengkap }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. HP:</span>
                        <span class="info-value">{{ $karyawan->no_hp }}</span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <ion-icon name="calendar-outline" class="info-icon"></ion-icon>
                    <h4>Informasi Karyawan</h4>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <span class="info-label">Mulai Masuk Kerja:</span>
                        <span class="info-value">
                            @if($karyawan->tanggal_masuk)
                                {{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d F Y') }}
                            @else
                                Belum diatur
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Masa Kerja Dari Awal:</span>
                        <span class="info-value">
                            @if($karyawan->tanggal_masuk)
                                @php
                                    $tanggalMasuk = \Carbon\Carbon::parse($karyawan->tanggal_masuk);
                                    $sekarang = \Carbon\Carbon::now();
                                    $diff = $tanggalMasuk->diff($sekarang);
                                    
                                    $tahun = $diff->y;
                                    $bulan = $diff->m;
                                    $hari = $diff->d;
                                    
                                    $masaKerja = '';
                                    if ($tahun > 0) {
                                        $masaKerja .= $tahun . ' tahun ';
                                    }
                                    if ($bulan > 0) {
                                        $masaKerja .= $bulan . ' bulan ';
                                    }
                                    if ($hari > 0 && $tahun == 0 && $bulan == 0) {
                                        $masaKerja .= $hari . ' hari';
                                    }
                                    
                                    if (empty($masaKerja)) {
                                        $masaKerja = 'Baru bergabung';
                                    }
                                @endphp
                                {{ trim($masaKerja) }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <ion-icon name="checkmark-circle-outline" class="info-icon"></ion-icon>
                    <h4>Status</h4>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <span class="info-label">Status Karyawan:</span>
                        <span class="info-value">
                            @php
                                // Cek status berdasarkan field status atau created_at/updated_at
                                $isActive = false;
                                
                                // Jika ada field status
                                if (isset($karyawan->status)) {
                                    $isActive = in_array(strtolower($karyawan->status), ['aktif', 'active', '1', 1, true]);
                                } 
                                // Jika tidak ada field status, cek berdasarkan keberadaan data
                                else {
                                    $isActive = $karyawan->exists;
                                }
                                
                                // Tambahan pengecekan: jika ada field deleted_at (soft delete)
                                if (isset($karyawan->deleted_at) && $karyawan->deleted_at !== null) {
                                    $isActive = false;
                                }
                            @endphp
                            
                            @if($isActive)
                                <span class="status-badge status-aktif">Aktif</span>
                            @else
                                <span class="status-badge status-nonaktif">Tidak Aktif</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Terakhir Login:</span>
                        <span class="info-value">
                            @if(isset($karyawan->last_login_at) && $karyawan->last_login_at)
                                {{ \Carbon\Carbon::parse($karyawan->last_login_at)->format('d F Y, H:i') }}
                            @elseif(isset($karyawan->updated_at) && $karyawan->updated_at)
                                {{ \Carbon\Carbon::parse($karyawan->updated_at)->format('d F Y, H:i') }}
                            @else
                                {{ date('d F Y, H:i') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Upload Foto -->
<div id="photoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ubah Foto Profil</h3>
            <button type="button" class="close-btn" onclick="closePhotoModal()">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <div class="modal-body">
            <form id="uploadPhotoForm" action="{{ route('profile.uploadPhoto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="upload-area" id="uploadArea">
                    <ion-icon name="cloud-upload-outline" class="upload-icon"></ion-icon>
                    <p>Klik atau drag foto ke sini</p>
                    <small>Format: JPG, PNG, JPEG (Max: 2MB)</small>
                </div>
                <input type="file" id="photoInput" name="foto_profil" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                <div class="preview-area" id="previewArea" style="display: none;">
                    <img id="previewImage" src="" alt="Preview">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePhotoModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>Upload Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <ion-icon name="sync-outline" class="loading-spinner"></ion-icon>
        <p>Mengupload foto...</p>
    </div>
</div>

<style>
.profile-container {
    padding: 1rem;
    max-width: 600px;
    margin: 0 auto;
}

.profile-photo-section {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    position: relative;
    overflow: hidden;
}

.profile-photo-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.profile-photo-wrapper {
    position: relative;
    z-index: 2;
    margin-bottom: 1rem;
}

.profile-photo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    border: 4px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
    position: relative;
}

.profile-photo-placeholder ion-icon {
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.8);
}

.profile-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.change-photo-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 auto;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.change-photo-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-1px);
}

.profile-name {
    position: relative;
    z-index: 2;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.profile-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.info-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-icon {
    font-size: 1.2rem;
}

.info-card-header h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.info-content {
    padding: 1rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: #666;
    flex: 1;
}

.info-value {
    font-weight: 600;
    color: #333;
    text-align: right;
    flex: 1;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-aktif {
    background: #d4edda;
    color: #155724;
}

.status-nonaktif {
    background: #f8d7da;
    color: #721c24;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0.25rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-btn:hover {
    background: #f0f0f0;
}

.modal-body {
    padding: 1.5rem;
}

.upload-area {
    border: 2px dashed #ddd;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.upload-area:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.upload-icon {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.upload-area p {
    margin: 0 0 0.5rem 0;
    color: #666;
    font-weight: 500;
}

.upload-area small {
    color: #999;
}

.preview-area {
    text-align: center;
    margin-bottom: 1rem;
}

.preview-area img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.loading-content {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.loading-spinner {
    font-size: 3rem;
    color: #007bff;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

.loading-content p {
    margin: 0;
    color: #333;
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Alert Styles */
.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 10px;
    font-weight: 500;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-container {
        padding: 0.5rem;
        padding-bottom: 2rem;
    }
    
    .profile-photo-section {
        padding: 1.5rem 1rem;
    }
    
    .profile-photo-placeholder {
        width: 100px;
        height: 100px;
    }
    
    .profile-name {
        font-size: 1.3rem;
        color: #ffffff;
    }
    
    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .info-value {
        text-align: left;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}

/* Fix untuk bottom navigation overlap */
.section {
    margin-bottom: 120px !important;
}

body {
    padding-bottom: 120px;
}
</style>

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const photoInput = document.getElementById('photoInput');
    const previewImage = document.getElementById('previewImage');
    const previewArea = document.getElementById('previewArea');
    const uploadBtn = document.getElementById('uploadBtn');
    const form = document.getElementById('uploadPhotoForm');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Upload area click
    uploadArea.addEventListener('click', () => {
        photoInput.click();
    });

    // File input change
    photoInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            handleFile(e.target.files[0]);
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    function handleFile(file) {
        console.log('File selected:', file.name, file.size, file.type);
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            showAlert('Format file tidak didukung. Gunakan JPG, PNG, atau JPEG.', 'error');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showAlert('Ukuran file terlalu besar. Maksimal 2MB.', 'error');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            uploadArea.style.display = 'none';
            previewArea.style.display = 'block';
            uploadBtn.disabled = false;
        };
        reader.onerror = function() {
            showAlert('Gagal membaca file. Silakan coba lagi.', 'error');
        };
        reader.readAsDataURL(file);
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!photoInput.files || !photoInput.files[0]) {
            showAlert('Pilih foto terlebih dahulu.', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('foto_profil', photoInput.files[0]);
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        
        // Show loading
        showLoading(true);
        uploadBtn.disabled = true;
        
        // Make request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            
            if (!response.ok) {
                // Try to get error message from response
                let errorMessage = `Server error: ${response.status}`;
                if (contentType && contentType.includes('application/json')) {
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || errorData.error || errorMessage;
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }
                } else {
                    // For non-JSON responses, get text
                    try {
                        const errorText = await response.text();
                        console.error('Server response:', errorText);
                    } catch (e) {
                        console.error('Error reading response text:', e);
                    }
                }
                throw new Error(errorMessage);
            }
            
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Server tidak mengembalikan response JSON yang valid');
            }
        })
        .then(data => {
            console.log('Success response:', data);
            
            if (data.success) {
                // Update profile photo
                updateProfilePhoto(data.photo_url);
                closePhotoModal();
                showAlert('Foto profil berhasil diperbarui!', 'success');
            } else {
                throw new Error(data.message || 'Gagal mengupload foto');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showAlert(error.message || 'Terjadi kesalahan saat mengupload foto', 'error');
        })
        .finally(() => {
            showLoading(false);
            uploadBtn.disabled = false;
        });
    });

    function updateProfilePhoto(photoUrl) {
        const profileContainer = document.getElementById('profilePhotoContainer');
        const existingImg = profileContainer.querySelector('img');
        const existingIcon = profileContainer.querySelector('ion-icon');
        
        if (existingImg) {
            existingImg.src = photoUrl + '?t=' + Date.now();
        } else {
            if (existingIcon) {
                existingIcon.remove();
            }
            const newImg = document.createElement('img');
            newImg.src = photoUrl + '?t=' + Date.now();
            newImg.alt = 'Profile Photo';
            newImg.className = 'profile-photo-img';
            newImg.id = 'profileImage';
            profileContainer.appendChild(newImg);
        }
    }

    function showLoading(show) {
        loadingOverlay.style.display = show ? 'flex' : 'none';
    }

    function showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        // Insert at top of modal body
        const modalBody = document.querySelector('.modal-body');
        modalBody.insertBefore(alert, modalBody.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
});

function openPhotoModal() {
    document.getElementById('photoModal').style.display = 'block';
    // Clear any existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.style.display = 'none';
    
    // Reset form
    document.getElementById('photoInput').value = '';
    document.getElementById('previewArea').style.display = 'none';
    document.getElementById('uploadArea').style.display = 'block';
    document.getElementById('uploadBtn').disabled = true;
    
    // Clear alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('photoModal');
    if (e.target === modal) {
        closePhotoModal();
    }
});
</script>
@endpush
@endsection