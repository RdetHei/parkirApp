// Leaflet map for indoor parking with CRS.Simple and image overlay
(function() {
    const container = document.getElementById('parking-map');
    if (!container) return;

    console.log('Parking Map Initializing...', container.dataset);

    const imageUrl = container.dataset.imageUrl || '';
    const mapWidthStr = container.dataset.width || '1000';
    const mapHeightStr = container.dataset.height || '800';
    const mapWidth = parseInt(mapWidthStr) || 1000;
    const mapHeight = parseInt(mapHeightStr) || 800;
    const mapId = container.dataset.mapId || '';
    const bookUrlTemplate = container.dataset.bookUrlTemplate || null;
    const unbookUrlTemplate = container.dataset.unbookUrlTemplate || null;
    const csrfToken = container.dataset.csrfToken || null;
    const bookingEnabled = !!bookUrlTemplate && !!csrfToken;

    if (!imageUrl) {
        console.error('Map image URL is missing');
        return;
    }

    let map, slotsLayer, camerasLayer, userMarkerLayer;

    function initMap() {
        if (map) map.remove();

        map = L.map('parking-map', {
            crs: L.CRS.Simple,
            minZoom: -3,
            maxZoom: 3,
            zoomControl: false,
            attributionControl: false,
            preferCanvas: true
        });

        // Use slightly larger bounds for padding
        const bounds = [[0, 0], [mapHeight, mapWidth]];
        const overlay = L.imageOverlay(imageUrl, bounds);
        overlay.addTo(map);

        // Ensure fitBounds is called after everything is ready
        setTimeout(() => {
            map.invalidateSize();
            map.fitBounds(bounds);
        }, 100);

        overlay.on('load', () => {
            map.invalidateSize();
            map.fitBounds(bounds);
            const loader = document.getElementById('map-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 500);
            }
        });

        // Watch for sidebar collapse to invalidate size
        const observer = new MutationObserver(() => {
            map.invalidateSize();
        });
        observer.observe(document.body, { attributes: true, attributeFilter: ['data-sidebar'] });

        L.control.zoom({ position: 'topright' }).addTo(map);

        slotsLayer = L.layerGroup().addTo(map);
        camerasLayer = L.layerGroup().addTo(map);
        userMarkerLayer = L.layerGroup().addTo(map);

        fetchData();
    }

    function getSlotColor(status) {
        switch (status) {
            case 'occupied':
                return { border: '#ef4444', fill: '#ef4444', text: '#fff' }; // Red for occupied
            case 'reserved':
            case 'reserved-by-me':
                return { border: '#f59e0b', fill: '#f59e0b', text: '#fff' }; // Amber for reserved
            case 'empty':
            default:
                return { border: '#10b981', fill: '#10b981', text: '#fff' }; // Green for empty
        }
    }

    function buildSlotPopup(slot) {
        const statusLabel = slot.status === 'reserved-by-me' ? 'Milik Anda' : (slot.status.charAt(0).toUpperCase() + slot.status.slice(1));
        const color = getSlotColor(slot.status).border;
        const isMine = slot.status === 'reserved-by-me' || (slot.status === 'occupied' && slot.is_mine);
        const canCancel = !!slot.transaksi_id && slot.status === 'reserved-by-me';

        return `
            <div style="min-width:180px; padding: 12px; background: #0f172a; border-radius: 12px; color: #fff;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid rgba(255,255,255,0.1);">
                    <span style="font-size:14px; font-weight:900;">Slot ${slot.code || '—'}</span>
                    <span style="padding:2px 8px; background:${color}20; border:1px solid ${color}40; font-size:9px; font-weight:800; color:${color}; border-radius:6px; text-transform:uppercase;">${statusLabel}</span>
                </div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div>
                        <p style="font-size:9px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin:0;">Plat Nomor</p>
                        <p style="font-size:12px; font-weight:700; color:#fff; margin:2px 0 0 0;">${slot.vehicle_plate || '—'}</p>
                    </div>
                    ${slot.notes ? `
                    <div>
                        <p style="font-size:9px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin:0;">Catatan</p>
                        <p style="font-size:11px; color:#94a3b8; margin:2px 0 0 0;">${slot.notes}</p>
                    </div>` : ''}
                </div>
                ${bookingEnabled && slot.status === 'empty' ? `
                <div style="margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:10px; color: #10b981; font-weight:bold; text-align:center; margin:0;">Klik slot untuk booking</p>
                </div>` : ''}
                ${bookingEnabled && canCancel ? `
                <div style="margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.05);">
                    <button type="button"
                            onclick="window.cancelMySlotBooking && window.cancelMySlotBooking('${slot.transaksi_id}')"
                            style="width:100%; border:1px solid rgba(239,68,68,0.35); background:rgba(239,68,68,0.1); color:#f87171; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; padding:8px 10px; border-radius:10px; cursor:pointer;">
                        Batalkan Slot Saya
                    </button>
                </div>` : ''}
                ${bookingEnabled && isMine && !canCancel ? `
                <div style="margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:10px; color:#60a5fa; font-weight:700; text-align:center; margin:0;">Slot ini sedang dipakai, tidak bisa dibatalkan dari peta.</p>
                </div>` : ''}
            </div>
        `;
    }

    function renderSlots(slots) {
        if (!slotsLayer) return;
        slotsLayer.clearLayers();
        userMarkerLayer.clearLayers();

        slots.forEach(slot => {
            const y = mapHeight - slot.y - slot.height;
            const bounds = [[y, slot.x], [y + slot.height, slot.x + slot.width]];
            const colors = getSlotColor(slot.status);

            const rect = L.rectangle(bounds, {
                color: colors.border,
                weight: 2,
                fillColor: colors.fill,
                fillOpacity: 0.3,
                className: 'parking-slot-rect'
            });

            rect.bindPopup(buildSlotPopup(slot), {
                className: 'modern-popup',
                offset: [0, -5]
            });

            rect.addTo(slotsLayer);

            // Special marker for my slot
            if (slot.status === 'reserved-by-me' || (slot.status === 'occupied' && slot.is_mine)) {
                const centerY = y + (slot.height / 2);
                const centerX = slot.x + (slot.width / 2);

                const userIcon = L.divIcon({
                    className: 'user-location-marker',
                    html: `
                        <div style="position:relative; display:flex; align-items:center; justify-content:center;">
                            <div style="position:absolute; width:30px; height:30px; background:#3b82f640; border-radius:50%; animation:ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
                            <div style="width:16px; height:16px; background:#3b82f6; border:2px solid #fff; border-radius:50%; box-shadow:0 0 10px rgba(59,130,246,0.5);"></div>
                        </div>
                    `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                L.marker([centerY, centerX], { icon: userIcon, zIndexOffset: 1000 })
                    .bindPopup('<div style="padding:5px 10px; font-weight:bold; font-size:11px; color:#fff; background:#1e293b; border-radius:6px;">Lokasi Anda</div>', { className: 'modern-popup' })
                    .addTo(userMarkerLayer);
            }

            if (bookingEnabled) {
                rect.on('click', async function() {
                    if (slot.status === 'empty') {
                        handleBooking(slot);
                    } else if (slot.status === 'reserved-by-me' && slot.transaksi_id) {
                        handleUnbooking(slot.transaksi_id);
                    }
                });
            }
        });
    }

    async function handleBooking(slot) {
        const kendaraanEl = document.getElementById('booking_kendaraan_id');
        const tarifEl = document.getElementById('booking_tarif_id');
        const kendaraanId = kendaraanEl ? kendaraanEl.value : '';
        const tarifId = tarifEl ? tarifEl.value : '';

        if (!kendaraanId) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'Pilih Kendaraan', text: 'Silakan pilih kendaraan terlebih dahulu.', background: '#0f172a', color: '#fff' });
            } else {
                alert('Pilih kendaraan terlebih dahulu.');
            }
            return;
        }

        try {
            const url = bookUrlTemplate.replace('AREA_ID_PLACEHOLDER', encodeURIComponent(mapId));
            const body = new URLSearchParams();
            body.append('_token', csrfToken);
            if (slot.id) body.append('parking_map_slot_id', slot.id);
            if (slot.code) body.append('slot_code', slot.code);
            body.append('id_kendaraan', kendaraanId);
            if (tarifId) body.append('id_tarif', tarifId);

            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                body: body.toString()
            });

            const data = await response.json();
            if (response.ok) {
                if (window.Swal) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Slot berhasil dipesan.', timer: 2000, showConfirmButton: false, background: '#0f172a', color: '#fff' });
                }
                fetchData();
            } else {
                throw new Error(data.message || 'Gagal melakukan booking');
            }
        } catch (err) {
            console.error(err);
            alert(err.message);
        }
    }

    async function handleUnbooking(transId) {
        if (!confirm('Batalkan pesanan slot ini?')) return;

        try {
            const url = unbookUrlTemplate.replace('TRANS_ID_PLACEHOLDER', encodeURIComponent(transId));
            const body = new URLSearchParams();
            body.append('_token', csrfToken);
            body.append('_method', 'DELETE');

            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                body: body.toString()
            });

            if (response.ok) {
                fetchData();
            } else {
                const data = await response.json();
                throw new Error(data.message || 'Gagal membatalkan booking');
            }
        } catch (err) {
            console.error(err);
            alert(err.message);
        }
    }

    // Expose cancel action for popup button
    window.cancelMySlotBooking = function(transId) {
        if (!transId) return;
        handleUnbooking(transId);
    };

    function renderCameras(cameras) {
        if (!camerasLayer) return;
        camerasLayer.clearLayers();

        cameras.forEach(cam => {
            const y = mapHeight - cam.y;
            const icon = L.divIcon({
                className: 'camera-icon',
                html: `<div style="width:30px;height:30px;background:#0d1526;border:1px solid rgba(255,255,255,0.15);border-radius:9px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.4);">
                    <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([y, cam.x], { icon });
            marker.bindPopup(`
                <div style="width:220px; overflow:hidden; border-radius:12px; background:#0d1526;">
                    <div style="padding:10px 12px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:11px; font-weight:800; color:#fff; text-transform:uppercase; letter-spacing:.05em;">CCTV: ${cam.name || 'Cam'}</span>
                        <span style="width:6px; height:6px; background:#10b981; border-radius:50%; box-shadow:0 0 8px #10b981;"></span>
                    </div>
                    <div style="aspect-ratio:16/9; background:#000; display:flex; align-items:center; justify-content:center; position:relative;">
                        ${cam.stream_url ? `<img src="${cam.stream_url}" style="width:100%; height:100%; object-fit:cover;">` : '<div style="text-align:center;"><svg style="width:24px;height:24px;color:#334155;margin-bottom:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg><p style="color:#475569;font-size:9px;font-weight:bold;margin:0;">NO SIGNAL</p></div>'}
                    </div>
                </div>`, { className: 'modern-popup !p-0', offset: [0, -10] });
            camerasLayer.addLayer(marker);
        });
    }

    function updateSummary(s) {
        const el = document.getElementById('parking-map-summary');
        if (!el) return;

        el.innerHTML = [
            { label: 'Total', val: s.total, color: '#fff' },
            { label: 'Tersedia', val: s.empty, color: '#10b981' },
            { label: 'Terisi', val: s.occupied, color: '#64748b' },
            { label: 'Reserved', val: s.reserved, color: '#f59e0b' },
        ].map(r => `
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:12px; color:#64748b; font-weight:500;">${r.label}</span>
                <span style="font-size:15px; font-weight:900; color:${r.color};">${r.val}</span>
            </div>`).join('');

        const pct = s.total > 0 ? Math.round(s.occupied / s.total * 100) : 0;
        const bar = document.getElementById('util-bar');
        const txt = document.getElementById('util-pct');
        if (bar) {
            bar.style.width = pct + '%';
            bar.style.background = pct >= 90 ? '#ef4444' : pct >= 70 ? '#f59e0b' : '#10b981';
        }
        if (txt) txt.textContent = pct + '%';
    }

    async function fetchData() {
        try {
            const url = `/api/parking-map/data?map_id=${encodeURIComponent(mapId)}`;
            const response = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) return;
            const data = await response.json();

            if (data.summary) updateSummary(data.summary);
            if (data.slots) renderSlots(data.slots);
            if (data.cameras) renderCameras(data.cameras);
        } catch (e) { console.error('Map fetch error:', e); }
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }

    // Refresh data periodically
    setInterval(fetchData, 10000);

    // Global refresh function
    window.refreshParkingMap = fetchData;

    // Handle resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => { if (map) map.invalidateSize(); }, 200);
    });

    // CSS for animations
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes ping {
            0% { transform: scale(1); opacity: 1; }
            75%, 100% { transform: scale(2.5); opacity: 0; }
        }
        .parking-slot-rect { cursor: pointer; transition: all 0.3s ease; }
        .parking-slot-rect:hover { fill-opacity: 0.5 !important; stroke-width: 3px !important; }
    `;
    document.head.appendChild(style);
})();
