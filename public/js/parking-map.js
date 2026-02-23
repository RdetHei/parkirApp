// Leaflet map untuk indoor parking dengan CRS.Simple dan image overlay

const container = document.getElementById('parking-map');
if (!container) {
    // Tidak ada container, jangan lanjut
    // (mis. view belum ter-render)
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

    function renderSlots(slots) {
        slotsLayer.clearLayers();

        slots.forEach(slot => {
            const topLeft = L.latLng(slot.y, slot.x);
            const bottomRight = L.latLng(slot.y + slot.height, slot.x + slot.width);
            const rectBounds = [topLeft, bottomRight];

            const rect = L.rectangle(rectBounds, {
                color: getSlotColor(slot.status),
                weight: 1,
                fillColor: getSlotColor(slot.status),
                fillOpacity: 0.6
            });

            const popupHtml = `
                <div class="text-sm">
                    <div><strong>Code:</strong> ${slot.code}</div>
                    <div><strong>Status:</strong> ${slot.status}</div>
                    <div><strong>Vehicle:</strong> ${slot.vehicle_plate ?? '-'}</div>
                </div>
            `;

            rect.bindPopup(popupHtml);
            rect.addTo(slotsLayer);
        });
    }

    async function fetchSlots() {
        try {
            const url = MAP_ID ? `/api/parking-slots?map_id=${MAP_ID}` : '/api/parking-slots';
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) {
                console.error('Gagal memuat data slot parkir', response.status);
                return;
            }
            const data = await response.json();
            renderSlots(Array.isArray(data) ? data : []);
        } catch (error) {
            console.error('Error fetch /api/parking-slots', error);
        }
    }

    fetchSlots();
    setInterval(fetchSlots, 5000);
}

