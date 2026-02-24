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

    function getSlotColor(status) {
        switch (status) {
            case 'occupied':
                return '#dc2626';
            case 'reserved':
                return '#ea580c';
            case 'empty':
            default:
                return '#16a34a';
        }
    }

    function updateSummary(summary) {
        const el = document.getElementById('parking-map-summary');
        if (!el) return;
        if (!summary) {
            el.textContent = '—';
            return;
        }
        el.innerHTML = [
            '<strong>Total slot:</strong> ' + summary.total,
            '<strong>Kosong:</strong> ' + summary.empty,
            '<strong>Terisi:</strong> ' + summary.occupied,
            '<strong>Reserved:</strong> ' + summary.reserved
        ].join(' \u2022 ');
    }

    function buildSlotPopup(slot) {
        const parts = [
            '<div class="text-sm space-y-1">',
            '<div><strong>Code:</strong> ' + (slot.code || '-') + '</div>',
            '<div><strong>Status:</strong> ' + (slot.status || 'empty') + '</div>',
            '<div><strong>Kendaraan:</strong> ' + (slot.vehicle_plate || '-') + '</div>'
        ];
        if (slot.area_name) {
            parts.push('<div><strong>Area:</strong> ' + slot.area_name + '</div>');
        }
        if (slot.notes) {
            parts.push('<div><strong>Catatan:</strong> ' + slot.notes + '</div>');
        }
        if (slot.meta && typeof slot.meta === 'object' && Object.keys(slot.meta).length) {
            parts.push('<div class="mt-1 text-xs text-gray-500">' + JSON.stringify(slot.meta) + '</div>');
        }
        parts.push('</div>');
        return parts.join('');
    }

    function renderSlots(slots) {
        slotsLayer.clearLayers();
        if (!Array.isArray(slots)) return;

        slots.forEach(function (slot) {
            const topLeft = L.latLng(slot.y, slot.x);
            const bottomRight = L.latLng(slot.y + slot.height, slot.x + slot.width);
            const rectBounds = [topLeft, bottomRight];

            const rect = L.rectangle(rectBounds, {
                color: getSlotColor(slot.status),
                weight: 1,
                fillColor: getSlotColor(slot.status),
                fillOpacity: 0.6
            });

            rect.bindPopup(buildSlotPopup(slot));
            rect.addTo(slotsLayer);
        });
    }

    function renderCameras(cameras) {
        camerasLayer.clearLayers();
        if (!Array.isArray(cameras) || cameras.length === 0) return;

        const iconSize = [24, 24];
        const icon = L.divIcon({
            className: 'parking-map-camera-marker',
            html: '<span style="display:inline-block;width:24px;height:24px;background:#6366f1;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,0.3);"></span>',
            iconSize: iconSize,
            iconAnchor: [12, 12]
        });

        cameras.forEach(function (cam) {
            const latLng = L.latLng(cam.y, cam.x);
            const marker = L.marker(latLng, { icon: icon });
            const popupHtml = '<div class="text-sm">' +
                '<div><strong>' + (cam.name || 'Kamera') + '</strong></div>' +
                '<div>Tipe: ' + (cam.type || '-') + '</div>' +
                (cam.url ? '<div class="text-xs text-gray-500 truncate max-w-[200px]">' + cam.url + '</div>' : '') +
                '</div>';
            marker.bindPopup(popupHtml);
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
