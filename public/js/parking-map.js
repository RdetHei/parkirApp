// Leaflet map untuk indoor parking dengan CRS.Simple dan image overlay

const container = document.getElementById('parking-map');
if (!container) {
    // Tidak ada container
} else {
    const FLOOR_IMAGE_URL = container.dataset.imageUrl || '/images/floor1.png';
    const FLOOR_WIDTH = parseInt(container.dataset.width || '1000', 10);
    const FLOOR_HEIGHT = parseInt(container.dataset.height || '800', 10);
    const MAP_ID = container.dataset.mapId || '';

    const imageBounds = [[0, 0], [FLOOR_HEIGHT, FLOOR_WIDTH]];

    const map = L.map('parking-map', {
        crs: L.CRS.Simple,
        minZoom: -2,
        maxZoom: 4,
        zoomControl: true,
        attributionControl: false
    });

    L.imageOverlay(FLOOR_IMAGE_URL, imageBounds).addTo(map);
    map.fitBounds(imageBounds);

    const slotsLayer = L.layerGroup().addTo(map);
    const camerasLayer = L.layerGroup().addTo(map);
    const userMarkerLayer = L.layerGroup().addTo(map);

    function getSlotColor(status) {
        switch (status) {
            case 'occupied':
                return {
                    border: '#dc2626',
                    fill: '#fecaca',
                    text: '#991b1b'
                };
            case 'reserved':
                return {
                    border: '#ea580c',
                    fill: '#ffedd5',
                    text: '#9a3412'
                };
            case 'reserved-by-me':
                return {
                    border: '#2563eb',
                    fill: '#dbeafe',
                    text: '#1e40af'
                };
            case 'empty':
            default:
                return {
                    border: '#16a34a',
                    fill: '#dcfce7',
                    text: '#166534'
                };
        }
    }

    function updateSummary(summary) {
        const el = document.getElementById('parking-map-summary');
        if (!el) return;
        if (!summary) {
            el.innerHTML = '<span class="text-gray-400">Gagal memuat ringkasan.</span>';
            return;
        }
        
        el.innerHTML = `
            <div class="flex flex-wrap items-center gap-4 py-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                    <span class="font-medium text-gray-700">Total: ${summary.total}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="font-medium text-emerald-700">Kosong: ${summary.empty}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="font-medium text-red-700">Terisi: ${summary.occupied}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                    <span class="font-medium text-orange-700">Reserved: ${summary.reserved}</span>
                </div>
            </div>
        `;
    }

    function buildSlotPopup(slot) {
        const colors = getSlotColor(slot.status);
        const statusLabel = slot.status.charAt(0).toUpperCase() + slot.status.slice(1).replace('-', ' ');
        
        return `
            <div class="p-1 min-w-[180px]">
                <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Slot Detail</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background-color: ${colors.fill}; color: ${colors.text}; border: 1px solid ${colors.border}">
                        ${statusLabel}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">ID / Kode</span>
                        <span class="text-sm font-bold text-gray-900">${slot.code || slot.id || '-'}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Area</span>
                        <span class="text-sm font-semibold text-gray-700">${slot.area_name || '-'}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Kendaraan</span>
                        <span class="text-sm font-mono font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">${slot.vehicle_plate || '-'}</span>
                    </div>
                    ${slot.notes ? `
                    <div class="mt-2 pt-2 border-t border-gray-50">
                        <span class="text-[10px] text-gray-400 uppercase block mb-1">Catatan</span>
                        <p class="text-xs text-gray-600 italic">${slot.notes}</p>
                    </div>` : ''}
                </div>
            </div>
        `;
    }

    const bookUrlTemplate = container.dataset.bookUrlTemplate || null;
    const unbookUrlTemplate = container.dataset.unbookUrlTemplate || null;
    const csrfToken = container.dataset.csrfToken || null;
    const bookingEnabled = !!bookUrlTemplate && !!csrfToken;

    function renderSlots(slots) {
        slotsLayer.clearLayers();
        if (!Array.isArray(slots)) return;

        // Find my parked slot if any
        const mySlot = slots.find(s => s.status === 'reserved-by-me' || (s.status === 'occupied' && s.is_mine));
        if (mySlot) {
            const latLng = L.latLng(mySlot.y + (mySlot.height / 2), mySlot.x + (mySlot.width / 2));
            
            userMarkerLayer.clearLayers();
            const userIcon = L.divIcon({
                className: 'user-location-marker',
                html: `
                    <div class="relative flex items-center justify-center">
                        <div class="absolute w-12 h-12 bg-indigo-500/20 rounded-full animate-ping"></div>
                        <div class="w-8 h-8 bg-indigo-600 rounded-full border-4 border-white shadow-xl flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                `,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            L.marker(latLng, { icon: userIcon })
                .bindPopup('<div class="p-2 font-bold text-center">Lokasi Kendaraan Anda</div>')
                .addTo(userMarkerLayer);
        }

        slots.forEach(function (slot) {
            const topLeft = L.latLng(slot.y, slot.x);
            const bottomRight = L.latLng(slot.y + slot.height, slot.x + slot.width);
            const rectBounds = [topLeft, bottomRight];
            const colors = getSlotColor(slot.status);

            const rect = L.rectangle(rectBounds, {
                color: colors.border,
                weight: 2,
                fillColor: colors.fill,
                fillOpacity: 0.8,
                className: 'parking-slot-rect transition-all duration-300'
            });

            rect.bindPopup(buildSlotPopup(slot), {
                className: 'modern-popup',
                maxWidth: 300
            });
            rect.addTo(slotsLayer);

            // Hover effect
            rect.on('mouseover', function() {
                this.setStyle({
                    fillOpacity: 1,
                    weight: 3
                });
            });
            rect.on('mouseout', function() {
                this.setStyle({
                    fillOpacity: 0.8,
                    weight: 2
                });
            });

            if (bookingEnabled) {
                rect.on('click', async function () {
                    try {
                        if (slot.status === 'empty') {
                            if (!slot.area_id) return;
                            const kendaraanEl = document.getElementById('booking_kendaraan_id');
                            const tarifEl = document.getElementById('booking_tarif_id');
                            const kendaraanId = kendaraanEl ? kendaraanEl.value : '';
                            const tarifId = tarifEl ? tarifEl.value : '';
                            if (!kendaraanId) {
                                alert('Pilih kendaraan terlebih dahulu sebelum booking.');
                                return;
                            }
                            const url = bookUrlTemplate.replace('AREA_ID_PLACEHOLDER', encodeURIComponent(slot.area_id));
                            const body = new URLSearchParams();
                            body.append('_token', csrfToken);
                            if (slot.id) {
                                body.append('parking_map_slot_id', String(slot.id));
                            }
                            if (slot.code) {
                                body.append('slot_code', String(slot.code));
                            }
                            body.append('id_kendaraan', String(kendaraanId));
                            if (tarifId) {
                                body.append('id_tarif', String(tarifId));
                            }
                            await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: body.toString()
                            });
                        } else if (slot.status === 'reserved-by-me' && slot.transaksi_id && unbookUrlTemplate) {
                            const url = unbookUrlTemplate.replace('TRANS_ID_PLACEHOLDER', encodeURIComponent(slot.transaksi_id));
                            const body = new URLSearchParams();
                            body.append('_token', csrfToken);
                            body.append('_method', 'DELETE');
                            await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: body.toString()
                            });
                        }
                    } catch (err) {
                        console.error('Booking/unbooking slot error', err);
                    } finally {
                        fetchData();
                    }
                });
            }
        });
    }

    function renderCameras(cameras) {
        camerasLayer.clearLayers();
        if (!Array.isArray(cameras) || cameras.length === 0) return;

        const iconSize = [32, 32];
        const icon = L.divIcon({
            className: 'parking-map-camera-marker',
            html: `
                <div class="flex items-center justify-center w-8 h-8 bg-indigo-600 rounded-lg shadow-lg border-2 border-white text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
            `,
            iconSize: iconSize,
            iconAnchor: [16, 16]
        });

        cameras.forEach(function (cam) {
            const latLng = L.latLng(cam.y, cam.x);
            const marker = L.marker(latLng, { icon: icon });
            
            const popupHtml = `
                <div class="p-1 min-w-[150px]">
                    <div class="flex items-center gap-2 mb-2 border-b border-gray-100 pb-2">
                        <div class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-gray-900">${cam.name || 'Kamera'}</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-gray-400 uppercase">Tipe</span>
                            <span class="font-bold text-gray-700 px-1.5 py-0.5 bg-gray-100 rounded">${cam.type || '-'}</span>
                        </div>
                        ${cam.url ? `
                        <div class="mt-2">
                            <a href="${cam.url}" target="_blank" class="block w-full text-center py-1.5 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded hover:bg-indigo-100 transition-colors">
                                Lihat Live Feed
                            </a>
                        </div>` : ''}
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupHtml, {
                className: 'modern-popup',
                offset: [0, -5]
            });
            marker.addTo(camerasLayer);
        });
    }

    async function fetchData() {
        try {
            const url = MAP_ID ? '/api/parking-slots?map_id=' + encodeURIComponent(MAP_ID) : '/api/parking-slots';
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) {
                console.error('Gagal memuat data slot parkir', response.status);
                updateSummary(null);
                return;
            }
            const data = await response.json();

            if (data.slots) {
                renderSlots(data.slots);
            } else if (Array.isArray(data)) {
                renderSlots(data);
            } else {
                renderSlots([]);
            }

            if (data.cameras) {
                renderCameras(data.cameras);
            } else {
                renderCameras([]);
            }

            if (data.summary) {
                updateSummary(data.summary);
            } else {
                updateSummary(null);
            }
        } catch (error) {
            console.error('Error fetch /api/parking-slots', error);
            updateSummary(null);
        }
    }

    fetchData();
    setInterval(fetchData, 5000);
}
