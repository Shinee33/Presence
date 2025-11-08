@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader text-light" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Halaman Presensi</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
    <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        #map {
            height: 280px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .status-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }

        .location-status {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .location-status.valid {
            color: #28a745;
        }

        .location-status.invalid {
            color: #dc3545;
        }

        .btn-presensi {
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-presensi:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .webcam-container {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            z-index: 10;
        }

        .distance-info {
            font-size: 14px;
            margin-top: 5px;
        }
    </style>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <input type="hidden" id="distance" value="0">
            
            {{-- Status Info --}}
            <div class="status-info">
                <h6><ion-icon name="location-outline"></ion-icon> Posisi Sekarang</h6>
                <div id="location-status" class="location-status">
                    <span id="status-text">Mengambil lokasi...</span>
                </div>
                <div id="distance-info" class="distance-info"></div>
            </div>
            
            {{-- Webcam Container --}}
            <div class="webcam-container">
                <div id="loading-overlay" class="loading-overlay" style="display: none;">
                    <div>
                        <ion-icon name="camera-outline" style="font-size: 48px; margin-bottom: 10px;"></ion-icon>
                        <div>Mengambil foto...</div>
                    </div>
                </div>
                <div class="webcam-capture"></div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col">
            @if ($cek > 0)
                <button id="takeabsen" class="btn btn-danger btn-block btn-presensi" disabled>
                    <ion-icon name="camera-outline"></ion-icon>
                    Presensi Pulang
                </button>
            @else
                <button id="takeabsen" class="btn btn-primary btn-block btn-presensi" disabled>
                    <ion-icon name="camera-outline"></ion-icon>
                    Presensi Masuk
                </button>
            @endif
        </div>    
    </div>
    
    <div class="row mt-3">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        // Configuration - dapat diambil dari config Laravel
        const CONFIG = {
            office: {
                lat: -1.6341801515074925,
                lng: 103.54967400825598,
                radius: 10000 // dalam meter
            },
            webcam: {
                width: 600,
                height: 380,
                image_format: 'png',
                png_quality: 80
            },
            map: {
                defaultZoom: 15,
                tileLayer: 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }
        };

        // Global variables
        let userLocation = null;
        let map = null;
        let isLocationValid = false;
        let webcamReady = false;

        // Initialize application
        $(document).ready(function() {
            initializeWebcam();
            initializeGeolocation();
        });

        // Initialize webcam
        function initializeWebcam() {
            try {
                Webcam.set({
                    width: CONFIG.webcam.width,
                    height: CONFIG.webcam.height,
                    image_format: CONFIG.webcam.image_format,
                    png_quality: CONFIG.webcam.png_quality,
                    constraints: {
                        width: { ideal: CONFIG.webcam.width },
                        height: { ideal: CONFIG.webcam.height }
                    }
                });

                Webcam.attach('.webcam-capture');
                
                // Check if webcam is ready
                setTimeout(() => {
                    if (Webcam.live) {
                        webcamReady = true;
                        updateButtonState();
                        console.log('Webcam ready');
                    } else {
                        showError('Kamera tidak dapat diakses. Pastikan kamera hanya untuk saya jangan berbagi');
                    }
                }, 2000);

            } catch (error) {
                console.error('Webcam initialization failed:', error);
                showError('Tidak dapat mengakses kamera. Pastikan kamera diizinkan dan coba refresh ulang halaman yach.');
            }
        }

        // Initialize geolocation
        function initializeGeolocation() {
            if (!navigator.geolocation) {
                showError('Browser Anda tidak mendukung geolocation.');
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 60000
            };

            navigator.geolocation.getCurrentPosition(
                successCallback, 
                errorCallback, 
                options
            );
        }

        // Geolocation success callback
        function successCallback(position) {
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            // Set location input
            document.getElementById('lokasi').value = userLocation.lat + "," + userLocation.lng;

            // Initialize map
            initializeMap();

            // Check location validity
            checkLocationValidity();

            // Update button state
            updateButtonState();
        }

        // Geolocation error callback
        function errorCallback(error) {
            let errorMessage = 'Tidak dapat mengakses lokasi. ';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += 'Akses lokasi tidak ditemukan. Izinkan akses lokasi untuk melanjutkan.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage += 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                    break;
                case error.TIMEOUT:
                    errorMessage += 'Timeout dalam mengakses lokasi. Coba lagi.';
                    break;
                default:
                    errorMessage += 'Error tidak diketahui.';
                    break;
            }
            
            showError(errorMessage);
            updateLocationStatus('Gagal mengambil lokasi', false);
        }

        // Initialize map
        function initializeMap() {
            if (!userLocation) return;

            // Create map
            map = L.map('map').setView([userLocation.lat, userLocation.lng], CONFIG.map.defaultZoom);

            // Add tile layer
            L.tileLayer(CONFIG.map.tileLayer, {
                maxZoom: 19,
                attribution: CONFIG.map.attribution
            }).addTo(map);

            // Add user marker
            const userMarker = L.marker([userLocation.lat, userLocation.lng])
                .addTo(map)
                .bindPopup('📍 Lokasi Anda')
                .openPopup();

            // Add office circle
            const officeCircle = L.circle([CONFIG.office.lat, CONFIG.office.lng], {
                color: '#007bff',
                fillColor: '#007bff',
                fillOpacity: 0.2,
                radius: CONFIG.office.radius
            }).addTo(map);

            // Add office marker
            const officeMarker = L.marker([CONFIG.office.lat, CONFIG.office.lng])
                .addTo(map)
                .bindPopup('🏢 Kantor');

            // Fit map to show both locations
            const group = new L.featureGroup([userMarker, officeMarker]);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Check location validity
        function checkLocationValidity() {
            if (!userLocation) {
                isLocationValid = false;
                updateLocationStatus('Lokasi tidak tersedia', false);
                return false;
            }

            // Calculate distance
            const distance = calculateDistance(
                userLocation.lat, userLocation.lng,
                CONFIG.office.lat, CONFIG.office.lng
            );

            document.getElementById('distance').value = distance;
            
            isLocationValid = distance <= CONFIG.office.radius;
            
            if (isLocationValid) {
                updateLocationStatus('Lokasi valid untuk presensi', true);
                updateDistanceInfo(distance, true);
            } else {
                updateLocationStatus('Lokasi terlalu jauh dari kantor', false);
                updateDistanceInfo(distance, false);
            }

            return isLocationValid;
        }

        // Calculate distance using Haversine formula
        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371e3; // Earth's radius in meters
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lng2-lng1) * Math.PI/180;

            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                      Math.cos(φ1) * Math.cos(φ2) *
                      Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            return R * c;
        }

        // Update location status display
        function updateLocationStatus(message, isValid) {
            const statusElement = document.getElementById('location-status');
            const statusText = document.getElementById('status-text');
            
            statusText.textContent = message;
            statusElement.className = 'location-status ' + (isValid ? 'valid' : 'invalid');
        }

        // Update distance info display
        function updateDistanceInfo(distance, isValid) {
            const distanceElement = document.getElementById('distance-info');
            const distanceText = distance < 1000 ? 
                `${Math.round(distance)}m dari kantor` : 
                `${(distance/1000).toFixed(1)}km dari kantor`;
            
            const maxDistance = CONFIG.office.radius < 1000 ? 
                `${CONFIG.office.radius}m` : 
                `${(CONFIG.office.radius/1000).toFixed(1)}km`;
            
            distanceElement.innerHTML = `
                <strong>${distanceText}</strong> (Maksimal: ${maxDistance})
            `;
        }

        // Update button state
        function updateButtonState() {
            const button = document.getElementById('takeabsen');
            const canTakePresensi = webcamReady && isLocationValid && userLocation;
            
            button.disabled = !canTakePresensi;
            
            if (canTakePresensi) {
                button.style.opacity = '1';
                button.style.cursor = 'pointer';
            } else {
                button.style.opacity = '0.6';
                button.style.cursor = 'not-allowed';
            }
        }

        // Show error message
        function showError(message) {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }

        // Presensi button click handler
        $("#takeabsen").click(function(e) {
            e.preventDefault();
            
            if (!webcamReady) {
                showError('Kamera belum siap nich. Tunggu sebentar dan coba lagi.');
                return;
            }
            
            if (!isLocationValid) {
                showError('Lokasi Kayaknya Kejauhan Nih. Coba Pastikan Lagi Sambil Lihat GPS Hidup Atau Tidak');
                return;
            }

            // Show confirmation
            Swal.fire({
                title: 'Meyakinkan Diri Mau Presensi',
                text: ' Yakin Ni Mau Presensi?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Sangat Yakin',
                cancelButtonText: 'Nanti Dulu Lah'
            }).then((result) => {
                if (result.isConfirmed) {
                    takePresensi();
                }
            });
        });

        // Take presensi
        function takePresensi() {
            // Show loading overlay
            document.getElementById('loading-overlay').style.display = 'flex';
            
            // Show loading dialog
            Swal.fire({
                title: 'Memproses Presensi',
                text: 'Sedang mengambil foto dan memproses data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Capture photo
            Webcam.snap(function(uri) {
                // Hide loading overlay
                document.getElementById('loading-overlay').style.display = 'none';
                
                if (!uri) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal mengambil foto. Silakan coba lagi.',
                        icon: 'error'
                    });
                    return;
                }

                const image = uri;
                const lokasi = document.getElementById('lokasi').value;
                const distance = document.getElementById('distance').value;

                // Validate data
                if (!lokasi) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Data lokasi tidak tersedia. Refresh halaman dan coba lagi.',
                        icon: 'error'
                    });
                    return;
                }

                // Send AJAX request
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/presensi/store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image,
                        lokasi: lokasi,
                        distance: distance
                    },
                    cache: false,
                    timeout: 30000,
                    success: function(respond) {
                        handlePresensiResponse(respond);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        let errorMessage = 'Terjadi kesalahan pada server.';
                        
                        if (xhr.status === 422) {
                            errorMessage = 'Data tidak valid. Silakan coba lagi.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Kesalahan server internal. Silakan coba lagi.';
                        } else if (status === 'timeout') {
                            errorMessage = 'Koneksi timeout. Periksa koneksi internet Anda.';
                        }
                        
                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            });
        }

        // Handle presensi response
        function handlePresensiResponse(respond) {
            const status = respond.split("|");
            
            if (status[0] === "success") {
                Swal.fire({
                    title: 'Presensi Berhasil! 🎉',
                    text: status[1],
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('presensi.create') }}";
                });
            } else {
                Swal.fire({
                    title: 'Presensi Gagal',
                    text: status[1],
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Auto-refresh location every 30 seconds
        setInterval(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    document.getElementById('lokasi').value = userLocation.lat + "," + userLocation.lng;
                    checkLocationValidity();
                    updateButtonState();
                }, function(error) {
                    console.warn('Location update failed:', error);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 30000
                });
            }
        }, 30000);

    </script>
@endpush