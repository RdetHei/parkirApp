@props([
    'targetInputId' => 'id_kendaraan',
    'targetInputType' => 'select',
    'onScanSuccess' => null,
    'ipWebcamUrl' => '',
    'cameras' => [], // Daftar kamera dari CRUD (id, nama, url, is_default)
])

<div x-data="plateScanner('{{ $targetInputId }}', '{{ $targetInputType }}', {{ $onScanSuccess ? "'{$onScanSuccess}'" : 'null' }}, {{ json_encode($ipWebcamUrl) }}, {{ json_encode($cameras) }})" class="w-full">
    <!-- Camera Section -->
    <div class="mb-6 animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center gap-2">
                <i class="fa-solid fa-camera-viewfinder text-emerald-500"></i>
                AI Plate Scanner
            </label>
            <div x-show="streamActive" class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Live Stream</span>
            </div>
        </div>

        <!-- Pilih Kamera (jika ada data kamera dari CRUD) -->
        <div x-show="cameras.length > 0" class="mb-5 p-4 bg-slate-950/50 border border-white/5 rounded-2xl group transition-all hover:border-emerald-500/20">
            <label for="plate-scanner-camera-select" class="block text-[9px] font-black text-slate-600 mb-2 uppercase tracking-widest group-hover:text-slate-400 transition-colors ml-1">Select Input Source</label>
            <div class="relative">
                <select id="plate-scanner-camera-select" x-model="selectedCameraId" x-on:change="onCameraChange()"
                        class="block w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs font-bold uppercase tracking-widest appearance-none cursor-pointer">
                    <template x-for="cam in cameras" :key="cam.id">
                        <option :value="cam.id" x-text="cam.nama + (cam.is_default ? ' (DEFAULT)' : '')" class="bg-slate-900"></option>
                    </template>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>
        </div>

        <!-- Camera Container -->
        <div class="relative bg-slate-950 rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl group" style="min-height: 400px;">
            <!-- Video Preview (kamera device) -->
            <video
                x-ref="video"
                x-show="!useIpWebcam && !capturedImage"
                autoplay
                playsinline
                class="w-full h-full object-cover"
                style="min-height: 400px;"
            ></video>

            <!-- IP Webcam stream (MJPEG dari HP) -->
            <img
                x-ref="ipWebcamStream"
                x-show="useIpWebcam && !capturedImage"
                :src="streamActive ? ipWebcamUrl : ''"
                x-on:error="if(streamActive) { errorMessage = 'Gagal memuat stream. Pastikan URL benar (contoh: http://localhost:8080/video) dan aplikasi IP Webcam sudah aktif.'; streamActive = false; }"
                class="w-full h-full object-cover"
                style="min-height: 400px;"
                alt="IP Webcam"
            />

            <!-- Captured Image Preview -->
            <img
                x-ref="capturedImage"
                x-show="capturedImage"
                class="w-full h-full object-contain bg-slate-950"
                style="min-height: 400px;"
            />

            <!-- Loading Overlay -->
            <div
                x-show="isLoading"
                class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-10"
            >
                <div class="text-center">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="absolute inset-0 border-4 border-emerald-500/20 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Processing Vision...</p>
                    <p class="text-[9px] text-slate-500 mt-2 uppercase tracking-widest">AI sedang menganalisa plat nomor</p>
                </div>
            </div>

            <!-- Scanner Controls Overlay -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-4 z-20">
                <!-- Snap Button -->
                <button
                    x-on:click="captureImage()"
                    x-show="!capturedImage && !isLoading"
                    type="button"
                    class="group relative flex items-center justify-center"
                >
                    <div class="absolute inset-0 bg-emerald-500 rounded-full blur-xl opacity-0 group-hover:opacity-40 transition-opacity"></div>
                    <div class="relative w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-2xl transition-transform active:scale-90 group-hover:scale-110">
                        <div class="w-12 h-12 border-4 border-slate-950 rounded-full"></div>
                    </div>
                </button>

                <!-- Reset Button -->
                <button
                    x-on:click="resetScanner()"
                    x-show="capturedImage && !isLoading"
                    type="button"
                    class="px-8 py-3 bg-rose-500 text-slate-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-rose-500/20 hover:bg-rose-400 transition-all active:scale-95 flex items-center gap-3"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset Scanner
                </button>
            </div>

            <!-- Error Message -->
            <div
                x-show="errorMessage"
                x-cloak
                class="absolute bottom-0 left-0 right-0 bg-red-600 text-white p-4 z-20"
            >
                <div class="flex items-center justify-between">
                    <span x-text="errorMessage"></span>
                    <button x-on:click="errorMessage = ''" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Success Message -->
            <div
                x-show="successMessage"
                x-cloak
                class="absolute bottom-0 left-0 right-0 bg-green-600 text-white p-4 z-20"
            >
                <div class="flex items-center justify-between">
                    <span x-text="successMessage"></span>
                    <button x-on:click="successMessage = ''" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Camera Controls -->
        <div class="mt-8 flex flex-wrap gap-4 justify-center animate-fade-in-up" style="animation-delay: 0.3s">
            <!-- Start Camera Button -->
            <button
                x-show="!streamActive && !capturedImage"
                x-on:click="startCamera()"
                type="button"
                class="px-8 py-3 bg-indigo-500 text-slate-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all active:scale-95 flex items-center gap-3"
            >
                <i class="fa-solid fa-video"></i>
                Buka Kamera
            </button>

            <!-- Capture Button -->
            <button
                x-show="streamActive && !capturedImage"
                x-on:click="captureImage()"
                type="button"
                class="px-8 py-3 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-emerald-500/20 hover:bg-emerald-400 transition-all active:scale-95 flex items-center gap-3"
            >
                <i class="fa-solid fa-camera"></i>
                Ambil Foto
            </button>

            <!-- Scan Button -->
            <button
                x-show="capturedImage && !isLoading"
                x-on:click="scanPlate()"
                type="button"
                :disabled="isLoading"
                class="px-8 py-3 bg-indigo-500 text-slate-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all active:scale-95 flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i class="fa-solid fa-microchip"></i>
                AI Analisa Plat
            </button>

            <!-- Retake Button -->
            <button
                x-show="capturedImage"
                x-on:click="retakePhoto()"
                type="button"
                class="px-8 py-3 bg-slate-800 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl border border-white/5 hover:bg-slate-700 hover:text-white transition-all active:scale-95 flex items-center gap-3"
            >
                <i class="fa-solid fa-rotate-left"></i>
                Ambil Ulang
            </button>

            <!-- Stop Camera Button -->
            <button
                x-show="streamActive"
                x-on:click="stopCamera()"
                type="button"
                class="px-8 py-3 bg-rose-500/10 text-rose-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl border border-rose-500/20 hover:bg-rose-500 hover:text-slate-950 transition-all active:scale-95 flex items-center gap-3"
            >
                <i class="fa-solid fa-power-off"></i>
                Tutup Kamera
            </button>
        </div>

        <!-- Scan Result Display -->
        <div x-show="scanResult" x-cloak class="mt-8 p-6 bg-slate-950/50 border border-white/5 rounded-[2rem] animate-fade-in">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <i class="fa-solid fa-magnifying-glass-chart text-sm"></i>
                    </div>
                    <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">AI Vision Result</h4>
                </div>
                <span
                    :class="scanResult.valid ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border-rose-500/20'"
                    class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border"
                    x-text="scanResult.valid ? 'Verified' : 'Unverified'"
                ></span>
            </div>
            <div class="flex flex-col gap-1">
                <p class="text-3xl font-black text-white tracking-tighter uppercase" x-text="scanResult.plate_number || 'UNKNOWN'"></p>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 h-1.5 bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500" :style="'width: ' + (scanResult.confidence * 100) + '%'"></div>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest" x-text="(scanResult.confidence * 100).toFixed(1) + '%'"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function plateScanner(targetInputId, targetInputType, onScanSuccess, ipWebcamUrl, cameras) {
    cameras = cameras || [];
    const defaultId = cameras.find(c => c.is_default)?.id || cameras[0]?.id;
    const fallbackUrl = (ipWebcamUrl || '').trim();
    return {
        cameras: cameras,
        selectedCameraId: defaultId || null,
        streamActive: false,
        capturedImage: null,
        isLoading: false,
        errorMessage: '',
        successMessage: '',
        scanResult: null,
        stream: null,
        get ipWebcamUrl() {
            if (this.cameras.length > 0 && this.selectedCameraId) {
                const cam = this.cameras.find(c => c.id == this.selectedCameraId);
                return (cam && cam.url) ? cam.url : '';
            }
            return fallbackUrl;
        },
        get useIpWebcam() {
            return this.ipWebcamUrl.length > 0;
        },

        onCameraChange() {
            if (this.streamActive) {
                this.streamActive = false;
                this.$nextTick(() => { this.streamActive = true; });
            }
        },

        async startCamera() {
            try {
                this.errorMessage = '';
                this.successMessage = '';

                if (this.useIpWebcam) {
                    // Beri saran otomatis jika URL hanya berisi IP dan port (tanpa path /video)
                    let finalUrl = this.ipWebcamUrl.trim();
                    if (finalUrl && !finalUrl.includes('/', 8)) { // Jika tidak ada path setelah http://...
                        this.errorMessage = 'Peringatan: URL sepertinya kurang lengkap. Gunakan http://localhost:8080/video untuk stream video.';
                    }
                    this.streamActive = true;
                } else {
                    // Kamera device: getUserMedia
                    const constraints = {
                        video: {
                            facingMode: 'environment',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    };
                    this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                    this.$refs.video.srcObject = this.stream;
                    this.streamActive = true;
                }
            } catch (error) {
                console.error('Camera error:', error);
                this.errorMessage = this.useIpWebcam
                    ? 'Tidak dapat mengakses IP Webcam. Pastikan HP terhubung (localhost:8080) dan app IP Webcam berjalan.'
                    : 'Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.';
                this.streamActive = false;
            }
        },

        stopCamera() {
            if (!this.useIpWebcam && this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.streamActive = false;
            this.capturedImage = null;
            this.scanResult = null;
        },

        captureImage() {
            try {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                if (this.useIpWebcam && this.$refs.ipWebcamStream) {
                    const img = this.$refs.ipWebcamStream;
                    if (!img.complete || img.naturalWidth === 0) {
                        this.errorMessage = 'Tunggu sebentar sampai stream IP Webcam siap.';
                        return;
                    }
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    ctx.drawImage(img, 0, 0);
                } else {
                    const video = this.$refs.video;
                    if (!video || video.videoWidth === 0) {
                        this.errorMessage = 'Kamera belum siap. Pastikan Anda sudah mengklik "Buka Kamera" dan mengizinkan akses kamera.';
                        return;
                    }
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0);
                }

                this.capturedImage = canvas.toDataURL('image/jpeg', 0.9);
                if (this.$refs.capturedImage) {
                    this.$refs.capturedImage.src = this.capturedImage;
                }
            } catch (e) {
                console.error('Capture error:', e);
                this.errorMessage = 'Gagal mengambil foto: ' + e.message;
            }
        },

        retakePhoto() {
            this.capturedImage = null;
            this.scanResult = null;
            this.errorMessage = '';
            this.successMessage = '';
        },

        resetScanner() {
            this.retakePhoto();
        },

        async scanPlate() {
            if (!this.capturedImage) {
                this.errorMessage = 'Silakan ambil foto terlebih dahulu';
                return;
            }

            this.isLoading = true;
            this.errorMessage = '';
            this.successMessage = '';
            this.scanResult = null;

            try {
                // Convert data URL to blob
                const response = await fetch(this.capturedImage);
                const blob = await response.blob();

                // Create FormData
                const formData = new FormData();
                formData.append('image', blob, 'plate-image.jpg');

                // Send to backend
                const scanResponse = await fetch('{{ route("api.scan-plate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                const data = await scanResponse.json();

                if (!scanResponse.ok) {
                    throw new Error(data.message || 'Gagal memproses gambar');
                }

                if (data.success) {
                    this.scanResult = {
                        plate_number: data.plate_number,
                        confidence: data.confidence,
                        valid: data.valid,
                        message: data.message
                    };

                    if (data.valid && data.plate_number) {
                        this.successMessage = data.message || 'Plat nomor berhasil dideteksi!';
                        this.fillTargetInput(data.plate_number);

                        // Call optional callback
                        if (onScanSuccess && typeof window[onScanSuccess] === 'function') {
                            window[onScanSuccess](data.plate_number, data);
                        }
                    } else {
                        this.errorMessage = data.message || 'Plat tidak valid atau tidak terdeteksi';
                    }
                } else {
                    throw new Error(data.message || 'Gagal memproses gambar');
                }

            } catch (error) {
                console.error('Scan error:', error);
                this.errorMessage = error.message || 'Terjadi kesalahan saat memproses gambar';
                this.scanResult = {
                    plate_number: null,
                    confidence: 0,
                    valid: false
                };
            } finally {
                this.isLoading = false;
            }
        },

        fillTargetInput(plateNumber) {
            const targetInput = document.getElementById(targetInputId);
            if (!targetInput) {
                console.warn('Target input not found:', targetInputId);
                return;
            }

            if (targetInputType === 'select') {
                // Find option by plate number
                const options = targetInput.options;
                for (let i = 0; i < options.length; i++) {
                    const optionText = options[i].text;
                    // Match plate number (format: "PLAT123 — jenis (pemilik)")
                    if (optionText.includes(plateNumber)) {
                        targetInput.value = options[i].value;
                        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                        break;
                    }
                }
            } else if (targetInputType === 'text') {
                targetInput.value = plateNumber;
                targetInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    };
}
</script>

