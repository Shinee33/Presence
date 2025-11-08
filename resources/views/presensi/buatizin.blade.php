<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Izin - BVS Presensi</title>
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .appHeader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: flex;
            align-items: center;
            padding: 0 16px;
            z-index: 1000;
        }

        .appHeader .left, .appHeader .right {
            width: 50px;
        }

        .appHeader .pageTitle {
            flex: 1;
            text-align: center;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 18px;
        }

        .headerButton {
            color: white;
            opacity: 0.9;
            font-size: 24px;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .headerButton:hover {
            opacity: 1;
        }

        .container {
            margin-top: 80px;
            padding: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-card h2 {
            color: #495057;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control.error {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .form-control.error + .error-message {
            display: block;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-option {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .status-option:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .status-option.selected {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .status-option input[type="radio"] {
            margin-right: 12px;
            transform: scale(1.2);
        }

        .status-icon {
            margin-right: 10px;
            font-size: 20px;
        }

        .izin-icon {
            color: #28a745;
        }

        .sakit-icon {
            color: #dc3545;
        }

        .date-input-wrapper {
            position: relative;
        }

        .date-input-wrapper::after {
            content: '📅';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 18px;
        }

        .success-animation {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .success-animation.show {
            display: block;
            animation: fadeInUp 0.5s ease;
        }

        .success-checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #28a745;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: checkmarkPop 0.5s ease;
        }

        .success-checkmark::after {
            content: '✓';
            color: white;
            font-size: 40px;
            font-weight: bold;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes checkmarkPop {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .history-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 15px;
        }

        .history-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .history-date {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .history-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-izin {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-sakit {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .status-approved {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-rejected {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .form-card {
                padding: 20px;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="appHeader">
        <div class="left">
            <a href="javascript:history.back()" class="headerButton">
                <span class="material-icons">arrow_back</span>
            </a>
        </div>
        <div class="pageTitle">Form Izin</div>
        <div class="right">
            <a href="#" class="headerButton" onclick="showHistory()">
                <span class="material-icons">history</span>
            </a>
        </div>
    </div>

    <div class="container">
        <!-- Form Card -->
        <div class="form-card" id="formCard">
            <h2>Ajukan Permohonan Izin</h2>
            
            <form id="frmizin">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <!-- Date Input -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="material-icons" style="vertical-align: middle; margin-right: 5px;">event</span>
                        Tanggal Izin
                    </label>
                    <div class="date-input-wrapper">
                        <input type="date" 
                               id="tgl_izin" 
                               name="tgl_izin" 
                               class="form-control"
                               min="{{ date('Y-m-d') }}"
                               required>
                    </div>
                    <div class="error-message">Tanggal izin harus dipilih</div>
                </div>

                <!-- Status Select -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="material-icons" style="vertical-align: middle; margin-right: 5px;">assignment</span>
                        Jenis Izin
                    </label>
                    
                    <div class="status-option" data-value="i">
                        <input type="radio" id="status_izin" name="status" value="i" required>
                        <span class="material-icons status-icon izin-icon">event_available</span>
                        <div>
                            <strong>Izin</strong>
                            <div style="font-size: 12px; color: #6c757d;">Keperluan pribadi, keluarga, dll</div>
                        </div>
                    </div>
                    
                    <div class="status-option" data-value="s">
                        <input type="radio" id="status_sakit" name="status" value="s" required>
                        <span class="material-icons status-icon sakit-icon">healing</span>
                        <div>
                            <strong>Sakit</strong>
                            <div style="font-size: 12px; color: #6c757d;">Tidak dapat hadir karena sakit</div>
                        </div>
                    </div>
                    <div class="error-message">Jenis izin harus dipilih</div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="material-icons" style="vertical-align: middle; margin-right: 5px;">description</span>
                        Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              class="form-control" 
                              placeholder="Jelaskan alasan izin/sakit Anda..."
                              rows="4"
                              required></textarea>
                    <div class="error-message">Keterangan harus diisi</div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="material-icons">send</span>
                    <span id="btnText">Kirim Permohonan</span>
                    <div id="btnSpinner" class="spinner" style="display: none;"></div>
                </button>
            </form>
        </div>

        <!-- Success Animation -->
        <div class="form-card success-animation" id="successAnimation">
            <div class="success-checkmark"></div>
            <h3 style="color: #28a745; margin-bottom: 10px;">Berhasil Dikirim!</h3>
            <p style="color: #6c757d; margin-bottom: 20px;">Permohonan izin Anda telah berhasil dikirim dan menunggu persetujuan.</p>
            <button type="button" class="btn btn-primary" onclick="resetForm()">
                <span class="material-icons">add</span>
                Buat Permohonan Baru
            </button>
        </div>

        <!-- History Card -->
        <div class="form-card" id="historyCard" style="display: none;">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center;">
                <span class="material-icons" style="margin-right: 10px;">history</span>
                Riwayat Izin
            </h3>
            <div id="historyContent">
                <div style="text-align: center; padding: 40px; color: #6c757d;">
                    <span class="material-icons" style="font-size: 48px; margin-bottom: 15px;">inbox</span>
                    <p>Belum ada riwayat izin</p>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="showForm()" style="margin-top: 15px;">
                <span class="material-icons">arrow_back</span>
                Kembali ke Form
            </button>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Application State
        const appState = {
            isSubmitting: false,
            submittedData: []
        };

        // DOM Elements
        const elements = {
            form: document.getElementById('frmizin'),
            formCard: document.getElementById('formCard'),
            successAnimation: document.getElementById('successAnimation'),
            historyCard: document.getElementById('historyCard'),
            historyContent: document.getElementById('historyContent'),
            submitBtn: document.getElementById('submitBtn'),
            btnText: document.getElementById('btnText'),
            btnSpinner: document.getElementById('btnSpinner')
        };

        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            loadHistoryFromStorage();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Form submission
            elements.form.addEventListener('submit', handleFormSubmit);

            // Status option selection
            document.querySelectorAll('.status-option').forEach(option => {
                option.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    updateStatusSelection();
                });
            });

            // Real-time validation
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('blur', validateField);
                input.addEventListener('input', clearError);
            });

            // Radio button change
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.addEventListener('change', updateStatusSelection);
            });
        }

        // Handle form submission - UPDATED TO SEND TO LARAVEL
        async function handleFormSubmit(e) {
            e.preventDefault();

            if (appState.isSubmitting) return;

            if (!validateForm()) {
                showValidationErrors();
                return;
            }

            setSubmittingState(true);

            try {
                const formData = new FormData(elements.form);
                
                // Debug: Log data yang akan dikirim
                console.log('Data yang akan dikirim:', {
                    tgl_izin: formData.get('tgl_izin'),
                    status: formData.get('status'),
                    keterangan: formData.get('keterangan')
                });
                
                // Kirim ke Laravel endpoint
                const response = await fetch('/presensi/storeizin', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(formData)
                });

                // Debug: Log response
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                let result;
                try {
                    result = await response.json();
                    console.log('Response data:', result);
                } catch (jsonError) {
                    console.error('Error parsing JSON:', jsonError);
                    const textResult = await response.text();
                    console.log('Raw response:', textResult);
                    throw new Error('Server mengembalikan response yang tidak valid');
                }

                if (response.ok && result.status === 'success') {
                    // Show success
                    showSuccess();
                    
                    // Optional: masih simpan ke localStorage untuk history lokal
                    const data = {
                        id: Date.now() + Math.random(), // Pastikan ID unik
                        tgl_izin: formData.get('tgl_izin'),
                        status: formData.get('status'),
                        keterangan: formData.get('keterangan'),
                        created_at: new Date().toISOString(),
                        status_approval: 'pending'
                    };
                    saveToStorage(data);

                    // Show success alert
                    Swal.fire({
                        title: 'Berhasil!',
                        text: result.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    throw new Error(result.message || `Server error: ${response.status}`);
                }

            } catch (error) {
                console.error('Error submitting form:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack
                });
                
                // Log untuk debugging
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    showError('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
                } else {
                    showError(error.message || 'Terjadi kesalahan saat mengirim data');
                }
            } finally {
                setSubmittingState(false);
            }
        }

        // Validate form
        function validateForm() {
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('error');
            });

            // Validate date
            const tglIzin = document.getElementById('tgl_izin');
            if (!tglIzin.value) {
                tglIzin.classList.add('error');
                isValid = false;
            }

            // Validate status
            const status = document.querySelector('input[name="status"]:checked');
            if (!status) {
                isValid = false;
            }

            // Validate keterangan
            const keterangan = document.getElementById('keterangan');
            if (!keterangan.value.trim()) {
                keterangan.classList.add('error');
                isValid = false;
            } else if (keterangan.value.trim().length < 10) {
                keterangan.classList.add('error');
                isValid = false;
            }

            return isValid;
        }

        // Show validation errors
        function showValidationErrors() {
            Swal.fire({
                title: 'Data Tidak Lengkap',
                text: 'Mohon lengkapi semua field yang diperlukan',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#667eea'
            });
        }

        // Validate individual field
        function validateField(e) {
            const field = e.target;
            if (field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('error');
            } else if (field.name === 'keterangan' && field.value.trim().length > 0 && field.value.trim().length < 10) {
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        }

        // Clear error on input
        function clearError(e) {
            e.target.classList.remove('error');
        }

        // Update status selection UI
        function updateStatusSelection() {
            document.querySelectorAll('.status-option').forEach(option => {
                option.classList.remove('selected');
            });

            const selectedRadio = document.querySelector('input[name="status"]:checked');
            if (selectedRadio) {
                const selectedOption = selectedRadio.closest('.status-option');
                selectedOption.classList.add('selected');
            }
        }

        // Set submitting state
        function setSubmittingState(isSubmitting) {
            appState.isSubmitting = isSubmitting;
            elements.submitBtn.disabled = isSubmitting;
            
            if (isSubmitting) {
                elements.btnText.style.display = 'none';
                elements.btnSpinner.style.display = 'inline-block';
            } else {
                elements.btnText.style.display = 'inline';
                elements.btnSpinner.style.display = 'none';
            }
        }

        // Show success animation
        function showSuccess() {
            elements.formCard.style.display = 'none';
            elements.successAnimation.classList.add('show');
        }

        // Reset form
        function resetForm() {
            elements.form.reset();
            elements.successAnimation.classList.remove('show');
            elements.formCard.style.display = 'block';
            elements.historyCard.style.display = 'none';
            
            // Clear selections
            document.querySelectorAll('.status-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Clear errors
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('error');
            });
        }

        // Show form
        function showForm() {
            elements.formCard.style.display = 'block';
            elements.historyCard.style.display = 'none';
            elements.successAnimation.classList.remove('show');
        }

        // Show history
        function showHistory() {
            elements.formCard.style.display = 'none';
            elements.successAnimation.classList.remove('show');
            elements.historyCard.style.display = 'block';
            loadHistoryFromStorage();
        }

        // Save to storage
        function saveToStorage(data) {
            try {
                let savedData = JSON.parse(localStorage.getItem('izin_history') || '[]');
                
                // Pastikan tidak ada duplikasi berdasarkan kombinasi tanggal, status, dan waktu
                const isDuplicate = savedData.some(item => 
                    item.tgl_izin === data.tgl_izin && 
                    item.status === data.status &&
                    Math.abs(new Date(item.created_at).getTime() - new Date(data.created_at).getTime()) < 5000 // dalam 5 detik
                );
                
                if (!isDuplicate) {
                    savedData.unshift(data); // Add to beginning
                    
                    // Keep only last 50 records
                    if (savedData.length > 50) {
                        savedData = savedData.slice(0, 50);
                    }
                    
                    localStorage.setItem('izin_history', JSON.stringify(savedData));
                    console.log('Data saved to localStorage:', data);
                } else {
                    console.log('Duplicate data, not saving to localStorage');
                }
                
                appState.submittedData = savedData;
            } catch (error) {
                console.error('Error saving to localStorage:', error);
            }
        }

        // Load history from storage
        function loadHistoryFromStorage() {
            const savedData = JSON.parse(localStorage.getItem('izin_history') || '[]');
            appState.submittedData = savedData;
            renderHistory();
        }

        // Render history
        function renderHistory() {
            const historyContent = elements.historyContent;
            
            if (appState.submittedData.length === 0) {
                historyContent.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #6c757d;">
                        <span class="material-icons" style="font-size: 48px; margin-bottom: 15px;">inbox</span>
                        <p>Belum ada riwayat izin</p>
                    </div>
                `;
                return;
            }

            const historyHtml = appState.submittedData.map(item => {
                const date = new Date(item.created_at);
                const statusText = item.status === 'i' ? 'Izin' : 'Sakit';
                const statusClass = item.status === 'i' ? 'status-izin' : 'status-sakit';
                const approvalClass = `status-${item.status_approval}`;
                const approvalText = {
                    'pending': 'Menunggu',
                    'approved': 'Disetujui',
                    'rejected': 'Ditolak'
                }[item.status_approval];

                return `
                    <div class="history-item">
                        <div style="flex: 1;">
                            <div class="history-date">${formatDate(item.tgl_izin)}</div>
                            <div style="margin-bottom: 8px;">
                                <span class="history-status ${statusClass}">${statusText}</span>
                                <span class="history-status ${approvalClass}">${approvalText}</span>
                            </div>
                            <div style="color: #6c757d; font-size: 14px;">${item.keterangan}</div>
                            <div style="color: #adb5bd; font-size: 12px; margin-top: 5px;">
                                Diajukan: ${date.toLocaleDateString('id-ID')} ${date.toLocaleTimeString('id-ID')}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            historyContent.innerHTML = historyHtml;
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Show error
        function showError(message) {
            Swal.fire({
                title: 'Gagal!',
                text: message,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        }
    </script>
</body>
</html>