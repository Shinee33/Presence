@extends('layouts.admin.tabler')

@section('content')

<!-- Custom Styles for Traffic Effect and Clock -->
<style>
    /* Enhanced Traffic Effect Animation */
    .card-traffic {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        height: 100%;
        min-height: 100px;
        border: none;
    }
    
    /* Primary Card - Elegant Blue */
    .card-primary {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #2196f3;
        color: #1565c0;
    }
    
    .card-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(33, 150, 243, 0.15), transparent);
        animation: traffic-flow-primary 3s infinite ease-in-out;
        z-index: 1;
    }
    
    /* Success Card - Fresh Green */
    .card-success {
        background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        border-left: 4px solid #4caf50;
        color: #2e7d32;
    }
    
    .card-success::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(76, 175, 80, 0.15), transparent);
        animation: traffic-flow-success 3.2s infinite ease-in-out;
        z-index: 1;
    }
    
    /* Info Card - Calm Teal */
    .card-info {
        background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
        border-left: 4px solid #009688;
        color: #00695c;
    }
    
    .card-info::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 150, 136, 0.15), transparent);
        animation: traffic-flow-info 2.8s infinite ease-in-out;
        z-index: 1;
    }
    
    /* Warning Card - Warm Orange */
    .card-warning {
        background: linear-gradient(135deg, #fff3e0 0%, #ffcc02 100%);
        border-left: 4px solid #ff9800;
        color: #ef6c00;
    }
    
    .card-warning::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 152, 0, 0.15), transparent);
        animation: traffic-flow-warning 3.5s infinite ease-in-out;
        z-index: 1;
    }
    
    /* Danger Card - Soft Red */
    .card-danger {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        border-left: 4px solid #f44336;
        color: #c62828;
    }
    
    .card-danger::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(244, 67, 54, 0.15), transparent);
        animation: traffic-flow-danger 2.9s infinite ease-in-out;
        z-index: 1;
    }
    
    .card-traffic:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .card-body {
        position: relative;
        z-index: 2;
        padding: 1.25rem;
    }
    
    /* Gentle traffic flow animations */
    @keyframes traffic-flow-primary {
        0% {
            left: -100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            left: 100%;
            opacity: 0;
        }
    }
    
    @keyframes traffic-flow-success {
        0% {
            left: -100%;
            opacity: 0;
            transform: scaleY(0.8);
        }
        50% {
            opacity: 1;
            transform: scaleY(1);
        }
        100% {
            left: 100%;
            opacity: 0;
            transform: scaleY(0.8);
        }
    }
    
    @keyframes traffic-flow-info {
        0% {
            left: -100%;
            opacity: 0;
            border-radius: 50px;
        }
        50% {
            opacity: 1;
            border-radius: 10px;
        }
        100% {
            left: 100%;
            opacity: 0;
            border-radius: 50px;
        }
    }
    
    @keyframes traffic-flow-warning {
        0% {
            left: -100%;
            opacity: 0;
            transform: skewX(-10deg);
        }
        50% {
            opacity: 1;
            transform: skewX(0deg);
        }
        100% {
            left: 100%;
            opacity: 0;
            transform: skewX(10deg);
        }
    }
    
    @keyframes traffic-flow-danger {
        0% {
            left: -100%;
            opacity: 0;
            height: 60%;
            top: 20%;
        }
        50% {
            opacity: 1;
            height: 100%;
            top: 0%;
        }
        100% {
            left: 100%;
            opacity: 0;
            height: 60%;
            top: 20%;
        }
    }
    
    /* Number animation */
    .number-pulse {
        animation: pulse-number 3s infinite ease-in-out;
        font-size: 2rem;
        font-weight: 800;
    }
    
    .card-primary .number-pulse {
        color: #1565c0;
    }
    
    .card-success .number-pulse {
        color: #2e7d32;
    }
    
    .card-info .number-pulse {
        color: #00695c;
    }
    
    .card-warning .number-pulse {
        color: #ef6c00;
    }
    
    .card-danger .number-pulse {
        color: #c62828;
    }
    
    @keyframes pulse-number {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.9;
            transform: scale(1.05);
        }
    }
    
    /* Avatar styling */
    .avatar {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        background: white !important;
    }
    
    .avatar-enhanced {
        transition: all 0.3s ease;
    }
    
    .avatar-enhanced:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Text styling */
    .card-text-muted {
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 0.5rem;
        opacity: 0.8;
    }
    
    .card-primary .card-text-muted {
        color: #1976d2;
    }
    
    .card-success .card-text-muted {
        color: #388e3c;
    }
    
    .card-info .card-text-muted {
        color: #00796b;
    }
    
    .card-warning .card-text-muted {
        color: #f57c00;
    }
    
    .card-danger .card-text-muted {
        color: #d32f2f;
    }
    
    .font-weight-medium {
        font-weight: 800 !important;
    }
    
    /* Digital Clock Styles - Back to Normal */
    .digital-clock {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-family: 'Courier New', monospace;
        font-size: 3rem;
        font-weight: bold;
        color: #212529;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        z-index: 10;
    }
    
    .clock-container {
    position: relative;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0.0); /* transparan */
    border: 2px dashed #6c757d; /* opsional: border hiasan */
    border-radius: 12px;
    margin: 2rem 0;
    box-shadow: none; /* atau hapus jika ingin bersih */
    overflow: hidden;
    }
    
    .clock-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: clock-traffic 4s infinite ease-in-out;
        z-index: 1;
    }
    
    @keyframes clock-traffic {
        0% {
            left: -100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            left: 100%;
            opacity: 0;
        }
    }
    
    /* Icon animation - more subtle */
    .icon-animate {
        transition: all 0.3s ease;
    }
    
    .card-primary .icon-animate {
        animation: icon-float 2.5s infinite ease-in-out;
    }
    
    .card-success .icon-animate {
        animation: icon-bounce 3s infinite ease-in-out;
    }
    
    .card-info .icon-animate {
        animation: icon-pulse 2.8s infinite ease-in-out;
    }
    
    .card-warning .icon-animate {
        animation: icon-sway 3.2s infinite ease-in-out;
    }
    
    .card-danger .icon-animate {
        animation: icon-wobble 2.7s infinite ease-in-out;
    }
    
    @keyframes icon-float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-3px);
        }
    }
    
    @keyframes icon-bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-4px);
        }
    }
    
    @keyframes icon-pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    @keyframes icon-sway {
        0%, 100% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(2deg);
        }
        75% {
            transform: rotate(-2deg);
        }
    }
    
    @keyframes icon-wobble {
        0%, 100% {
            transform: translateX(0);
        }
        25% {
            transform: translateX(-1px);
        }
        75% {
            transform: translateX(1px);
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .digital-clock {
            font-size: 2.5rem;
        }
        
        .clock-container {
            height: 100px;
        }
        
        .number-pulse {
            font-size: 1.6rem;
        }
        
        .avatar {
            width: 3rem;
            height: 3rem;
        }
    }
    
    /* Subtle particle effects - removed scary ones */
    .card-traffic::after {
        content: '';
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        animation: gentle-pulse 3s infinite ease-in-out;
        z-index: 1;
    }
    
    .card-success::after {
        animation-delay: -1s;
    }
    
    .card-info::after {
        animation-delay: -2s;
    }
    
    .card-warning::after {
        animation-delay: -1.5s;
    }
    
    .card-danger::after {
        animation-delay: -0.5s;
    }
    
    @keyframes gentle-pulse {
        0%, 100% {
            opacity: 0.3;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.2);
        }
    }
    @keyframes gentle-pulse {
        0%, 100% {
            opacity: 0.3;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.2);
        }
    }
    
    /* Row spacing */
    .stats-row {
        margin-bottom: 2rem;
    }
    
    /* Page header improvements */
    .page-pretitle {
        color: #6c757d;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .page-title {
        color: #495057;
        font-weight: 700;
        margin: 0;
    }
</style>

<!-- Page Header -->
<div class="page-header d-print-none">
    <div class="container-xl ps-4">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Dashboard Admin</h2>
            </div>
        </div>
    </div>
</div>

<!-- Page Body -->
<div class="page-body">
    <div class="container-xl ps-4">
        <!-- Statistics Cards Row -->
        <div class="row stats-row g-3">
            <div class="col-md-6 col-xl-2">
                <div class="card card-traffic">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-white text-primary avatar avatar-enhanced">
                                    <svg class="icon-animate" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                            </div>
                            
                            <div class="col">
                                <div class="font-weight-medium number-pulse">{{ $jumlahkaryawan }}</div>
                                <div class="card-text-muted">Total Karyawan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-2">
                <div class="card card-traffic">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-white text-success avatar avatar-enhanced">
                                    <svg class="icon-animate" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-fingerprint">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" />
                                        <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6" />
                                        <path d="M12 11v2a14 14 0 0 0 2.5 8" />
                                        <path d="M8 15a18 18 0 0 0 1.8 6" />
                                        <path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium number-pulse">{{ $rekappresensi->jmlhadir }}</div>
                                <div class="card-text-muted">Total Kehadiran</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-2">
                <div class="card card-traffic">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-white text-info avatar avatar-enhanced">
                                    <svg class="icon-animate" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-cancel">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                        <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M17 21l4 -4" />
                                    </svg> 
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium number-pulse">{{ $rekapizin->jmlizin ?? 0 }}</div>
                                <div class="card-text-muted">Karyawan Izin</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-2">
                <div class="card card-traffic">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-white text-warning avatar avatar-enhanced">
                                    <svg class="icon-animate" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-stethoscope">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M6 4h-1a2 2 0 0 0 -2 2v3.5h0a5.5 5.5 0 0 0 11 0v-3.5a2 2 0 0 0 -2 -2h-1" />
                                        <path d="M8 15a6 6 0 1 0 12 0v-3" />
                                        <path d="M11 3v2" />
                                        <path d="M6 3v2" />
                                        <path d="M20 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium number-pulse">{{ $rekapizin->jmlsakit ?? 0 }}</div>
                                <div class="card-text-muted">Karyawan Sakit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-2">
                <div class="card card-traffic">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-white text-danger avatar avatar-enhanced">
                                    <svg class="icon-animate" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium number-pulse">{{ $rekappresensi->jmlterlambat ?? 0 }}</div>
                                <div class="card-text-muted">Karyawan Terlambat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Digital Clock Section -->
        <div class="row">
            <div class="col-12">
                <div class="clock-container">
                    <div class="digital-clock" id="digitalClock">
                        00:00:00
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Real-time Clock -->
<script>
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const timeString = `${hours}:${minutes}:${seconds}`;
    const clockElement = document.getElementById('digitalClock');
    if (clockElement) {
        clockElement.textContent = timeString;
    }
}

// Update clock immediately and then every second
updateClock();
const clockInterval = setInterval(updateClock, 1000);

// Enhanced JavaScript for better functionality
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card-traffic');
    
    cards.forEach((card, index) => {
        // Add staggered animation delay
        card.style.animationDelay = `${index * 0.1}s`;
        
        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
        
        // Add click ripple effect
        card.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(0, 123, 255, 0.1);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
                z-index: 1000;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});

// Clean up interval on page unload
window.addEventListener('beforeunload', function() {
    if (typeof clockInterval !== 'undefined') {
        clearInterval(clockInterval);
    }
});
</script>

@endsection