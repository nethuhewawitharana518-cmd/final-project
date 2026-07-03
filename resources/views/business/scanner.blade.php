@extends('layouts.app')

@section('title', 'QR Scanner')

@push('styles')
<style>
    .scanner-viewport-wrapper {
        width: 320px;
        height: 320px;
        border-radius: var(--radius);
        overflow: hidden;
        background: #111111;
        border: 2px solid var(--border);
        box-shadow: var(--shadow);
        position: relative;
        margin: 0 auto;
    }
    
    .scanner-viewport-wrapper.active {
        border-color: var(--primary) !important;
        box-shadow: 0 0 25px rgba(255, 107, 0, 0.4) !important;
    }

    #reader {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }

    #reader video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: calc(var(--radius) - 2px);
    }

    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        box-sizing: border-box;
        border: 40px solid rgba(0, 0, 0, 0.5);
        z-index: 10;
        display: none;
    }

    .scanner-viewport-wrapper.active .scanner-overlay {
        display: block;
    }

    .scanner-line {
        position: absolute;
        top: 10%;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(to right, transparent, var(--primary), transparent);
        box-shadow: 0 0 10px var(--primary);
        animation: scan 2.5s linear infinite;
    }

    @keyframes scan {
        0% { top: 10%; }
        50% { top: 90%; }
        100% { top: 10%; }
    }

    .camera-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        z-index: 5;
    }

    .result-card {
        border-radius: var(--radius);
        background: var(--bg-surface-2) !important;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    /* Style overrides for library inner layout to keep it clean */
    #reader__status_span {
        display: none !important;
    }
    #reader__dashboard {
        display: none !important;
    }
    #reader video {
        transform: scaleX(-1); /* mirror preview */
    }
</style>
@endpush

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">QR Code Verification Scanner</h4>
            <span class="badge bg-success status-badge active">QR Reader</span>
        </div>

        <div class="content-area text-start">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <!-- Scanner Panel -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-4 text-center" id="scannerPanel">
                        <div class="scanner-viewport-wrapper mb-4" id="viewportWrapper">
                            <!-- HTML5 QR Code element -->
                            <div id="reader"></div>
                            
                            <!-- Overlay scanning line -->
                            <div class="scanner-overlay">
                                <div class="scanner-line"></div>
                            </div>

                            <!-- Camera placeholder before init -->
                            <div class="camera-placeholder" id="cameraPlaceholder">
                                <i class="fa fa-camera fa-3x mb-3 text-muted"></i>
                                <span class="small fw-semibold">Webcam stream inactive</span>
                            </div>
                        </div>

                        <div id="scannerInstructions">
                            <h5 class="fw-bold mb-2">Ready to Scan Customer Reservation QR Code</h5>
                            <p class="text-muted small px-3">Align the customer's QR code within the camera viewport above to instantly verify and mark the reservation as completed.</p>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-success px-4 py-2" id="startBtn">
                                <i class="fa fa-video me-2"></i>Initialize Camera
                            </button>
                            <button type="button" class="btn btn-danger px-4 py-2 d-none" id="stopBtn">
                                <i class="fa fa-video-slash me-2"></i>Stop Camera
                            </button>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-5 text-center d-none" id="loadingPanel">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="fw-bold">Verifying Code</h5>
                        <p class="text-muted small">Connecting to secure database to validate reservation details...</p>
                    </div>

                    <!-- Success Details Card -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-4 d-none" id="successPanel">
                        <div class="text-center mb-4">
                            <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2.2rem; background: rgba(16, 185, 129, 0.15); border: 2px solid rgba(16, 185, 129, 0.3);">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <h4 class="fw-bold text-success">Verification Successful</h4>
                            <p class="text-muted small mb-0">The order has been verified and marked as collected.</p>
                        </div>

                        <div class="result-card p-3 mb-4">
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-muted small fw-bold">Code:</span>
                                <span class="fw-bold text-dark" id="resCode"></span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-muted small fw-bold">Customer:</span>
                                <span class="fw-bold text-dark" id="resCustomer"></span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-muted small fw-bold">Phone:</span>
                                <span class="text-dark" id="resPhone"></span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-muted small fw-bold">Pickup Time:</span>
                                <span class="text-dark" id="resPickup"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small fw-bold">Total Paid:</span>
                                <span class="fw-bold text-success" id="resTotal"></span>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2">Items Claimed:</h6>
                        <ul class="list-group mb-4 shadow-sm" id="resItems">
                            <!-- JS populated -->
                        </ul>

                        <div class="text-center">
                            <button type="button" class="btn btn-outline-success px-4" id="scanAgainBtn">
                                <i class="fa fa-qrcode me-2"></i>Scan Next QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Error Details Card -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-4 text-center d-none" id="errorPanel">
                        <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 2.2rem; background: rgba(239, 68, 68, 0.15); border: 2px solid rgba(239, 68, 68, 0.3);">
                            <i class="fa fa-times-circle"></i>
                        </div>
                        <h4 class="fw-bold text-danger">Verification Failed</h4>
                        <p class="text-muted small mb-4" id="errorMsg"></p>

                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-danger px-4" id="retryBtn">
                                <i class="fa fa-redo me-2"></i>Try Scanning Again
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const retryBtn = document.getElementById('retryBtn');
        const scanAgainBtn = document.getElementById('scanAgainBtn');
        
        const scannerPanel = document.getElementById('scannerPanel');
        const loadingPanel = document.getElementById('loadingPanel');
        const successPanel = document.getElementById('successPanel');
        const errorPanel = document.getElementById('errorPanel');
        const viewportWrapper = document.getElementById('viewportWrapper');
        const cameraPlaceholder = document.getElementById('cameraPlaceholder');
        
        let html5QrCode = null;
        let isCameraRunning = false;

        // Initialize camera
        async function startScanner() {
            try {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                // Show running state overlay
                viewportWrapper.classList.add('active');
                cameraPlaceholder.classList.add('d-none');
                startBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');

                const config = { 
                    fps: 15, 
                    qrbox: function(width, height) {
                        return {
                            width: Math.min(width, 220),
                            height: Math.min(height, 220)
                        };
                    }
                };

                await html5QrCode.start(
                    { facingMode: "environment" }, 
                    config, 
                    onScanSuccess
                );
                
                isCameraRunning = true;
            } catch (err) {
                console.error("Camera access failed:", err);
                alert("Could not access webcam stream. Please verify camera permissions in your browser.");
                resetUI();
            }
        }

        // Stop camera
        async function stopScanner() {
            if (html5QrCode && isCameraRunning) {
                try {
                    await html5QrCode.stop();
                } catch (err) {
                    console.error("Failed to stop camera:", err);
                }
                isCameraRunning = false;
            }
            resetUI();
        }

        // On successful scan
        async function onScanSuccess(decodedText) {
            // Stop scanning immediately
            await stopScanner();

            // Display loading
            scannerPanel.classList.add('d-none');
            loadingPanel.classList.remove('d-none');

            // Send to verification endpoint
            try {
                const response = await fetch("{{ route('business.scanner.verify') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ qr_token: decodedText.trim() })
                });

                const data = await response.json();
                loadingPanel.classList.add('d-none');

                if (response.ok && data.status === 'success') {
                    // Populate success view
                    document.getElementById('resCode').textContent = data.order.reservation_code;
                    document.getElementById('resCustomer').textContent = data.order.customer_name;
                    document.getElementById('resPhone').textContent = data.order.customer_phone || 'N/A';
                    document.getElementById('resPickup').textContent = data.order.pickup_time;
                    document.getElementById('resTotal').textContent = data.order.total_amount;

                    const itemsList = document.getElementById('resItems');
                    itemsList.innerHTML = '';
                    data.order.items.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center bg-transparent border-secondary text-white';
                        li.innerHTML = `
                            <div>
                                <span class="fw-bold">${item.name}</span>
                                <span class="badge bg-secondary ms-2">x${item.quantity}</span>
                            </div>
                            <span class="text-success small fw-semibold">${item.price}</span>
                        `;
                        itemsList.appendChild(li);
                    });

                    successPanel.classList.remove('d-none');
                } else {
                    // Populate error view
                    document.getElementById('errorMsg').textContent = data.message || 'An unexpected error occurred during verification.';
                    errorPanel.classList.remove('d-none');
                }
            } catch (err) {
                console.error("AJAX validation failed:", err);
                loadingPanel.classList.add('d-none');
                document.getElementById('errorMsg').textContent = "Server connection lost. Please verify internet access and try again.";
                errorPanel.classList.remove('d-none');
            }
        }

        // Reset scanning UI state
        function resetUI() {
            viewportWrapper.classList.remove('active');
            cameraPlaceholder.classList.remove('d-none');
            startBtn.classList.remove('d-none');
            stopBtn.classList.add('d-none');
        }

        // Event listeners
        startBtn.addEventListener('click', startScanner);
        stopBtn.addEventListener('click', stopScanner);
        
        retryBtn.addEventListener('click', () => {
            errorPanel.classList.add('d-none');
            scannerPanel.classList.remove('d-none');
            startScanner();
        });

        scanAgainBtn.addEventListener('click', () => {
            successPanel.classList.add('d-none');
            scannerPanel.classList.remove('d-none');
            startScanner();
        });
    });
</script>
@endpush
