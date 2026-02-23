@props([
    'targetInputId' => 'id_kendaraan',
    'targetInputType' => 'select',
    'onScanSuccess' => null,
    'ipWebcamUrl' => '',
    'cameras' => [], // Daftar kamera dari CRUD (id, nama, url, is_default)
])

<div x-data="plateScanner('{{ $targetInputId }}', '{{ $targetInputType }}', {{ $onScanSuccess ? "'{$onScanSuccess}'" : 'null' }}, {{ json_encode($ipWebcamUrl) }}, {{ json_encode($cameras) }})" class="w-full">
    <!-- Camera Section -->
    <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Scan Plat Nomor
        </label>

        <!-- Pilih Kamera (jika ada data kamera dari CRUD) -->
        <div x-show="cameras.length > 0" class="mb-3">
            <label for="plate-scanner-camera-select" class="block text-xs font-medium text-gray-600 mb-1">Kamera</label>
            <select id="plate-scanner-camera-select" x-model="selectedCameraId" @change="onCameraChange()"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                <template x-for="cam in cameras" :key="cam.id">
                    <option :value="cam.id" x-text="cam.nama + (cam.is_default ? ' (default)' : '')"></option>
                </template>
            </select>
        </div>

        <!-- Camera Container -->
        <div class="relative bg-gray-900 rounded-xl overflow-hidden" style="min-height: 300px;">
            <!-- Video Preview (kamera device) -->
            <video
                x-ref="video"
                x-show="!useIpWebcam && !capturedImage"
                autoplay
                playsinline
                class="w-full h-full object-cover"
                style="min-height: 300px;"
            ></video>

            <!-- IP Webcam stream (MJPEG dari HP) -->
            <img
                x-ref="ipWebcamStream"
                x-show="useIpWebcam && !capturedImage"
                :src="streamActive ? ipWebcamUrl : ''"
                class="w-full h-full object-cover"
                style="min-height: 300px;"
                alt="IP Webcam"
            />

            <!-- Captured Image Preview -->
            <img
                x-ref="capturedImage"
                x-show="capturedImage"
                class="w-full h-full object-contain bg-black"
                style="min-height: 300px;"
            />

            <!-- Loading Overlay -->
            <div
                x-show="isLoading"
                class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center z-10"
            >
                <div class="text-center">
                    <svg class="animate-spin h-12 w-12 text-white mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-white font-semibold">Memproses gambar...</p>
                </div>
            </div>

            <!-- Error Message -->
            <div
                x-show="errorMessage"
                x-cloak
                class="absolute bottom-0 left-0 right-0 bg-red-600 text-white p-4 z-20"
            >
                <div class="flex items-center justify-between">
                    <span x-text="errorMessage"></span>
                    <button @click="errorMessage = ''" class="ml-4 text-white hover:text-gray-200">
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
                    <button @click="successMessage = ''" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Camera Controls -->
        <div class="mt-4 flex gap-2 justify-center">
            <!-- Start Camera Button -->
            <button
                x-show="!streamActive && !capturedImage"
                @click="startCamera()"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Buka Kamera
            </button>

            <!-- Capture Button -->
            <button
                x-show="streamActive && !capturedImage"
                @click="captureImage()"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Ambil Foto
            </button>

            <!-- Scan Button -->
            <button
                x-show="capturedImage && !isLoading"
                @click="scanPlate()"
                type="button"
                :disabled="isLoading"
                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Scan Plat
            </button>

            <!-- Retake Button -->
            <button
                x-show="capturedImage"
                @click="retakePhoto()"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Ambil Ulang
            </button>

            <!-- Stop Camera Button -->
            <button
                x-show="streamActive"
                @click="stopCamera()"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                </svg>
                Tutup Kamera
            </button>
        </div>

        <!-- Scan Result Display -->
        <div x-show="scanResult" x-cloak class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-semibold text-gray-700">Hasil Scan:</h4>
                <span
                    :class="scanResult.valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="px-2 py-1 rounded text-xs font-semibold"
                    x-text="scanResult.valid ? 'Valid' : 'Tidak Valid'"
                ></span>
            </div>
            <p class="text-lg font-bold text-gray-900" x-text="scanResult.plate_number || 'Tidak terdeteksi'"></p>
            <p class="text-xs text-gray-500 mt-1">
                Confidence: <span x-text="(scanResult.confidence * 100).toFixed(1) + '%'"></span>
            </p>
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
                    // IP Webcam: cukup aktifkan stream (img src sudah di-set di template)
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
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);
            }

            this.capturedImage = canvas.toDataURL('image/jpeg', 0.9);
            this.$refs.capturedImage.src = this.capturedImage;
        },

        retakePhoto() {
            this.capturedImage = null;
            this.scanResult = null;
            this.errorMessage = '';
            this.successMessage = '';
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

