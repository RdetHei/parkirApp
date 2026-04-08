/**
 * Neston Live Map Engine v2.0
 * Optimized for accuracy, performance and responsiveness.
 */

class ParkingMapEngine {
    constructor() {
        this.wrapper = document.getElementById('map-canvas-wrapper');
        this.viewport = document.getElementById('map-viewport');
        this.slotsLayer = document.getElementById('slots-layer');
        this.camerasLayer = document.getElementById('cameras-layer');
        this.loadingOverlay = document.getElementById('map-loading-overlay');
        
        // Base config from data attributes
        this.baseWidth = parseFloat(this.wrapper.dataset.width);
        this.baseHeight = parseFloat(this.wrapper.dataset.height);
        this.mapId = this.wrapper.dataset.id;

        // State
        this.scale = 1;
        this.translateX = 0;
        this.translateY = 0;
        this.isDragging = false;
        this.startX = 0;
        this.startY = 0;
        this.activeSlotId = null;
        this.data = null;

        this.init();
    }

    async init() {
        this.setupEventListeners();
        this.fitToViewport();
        await this.fetchData();
        this.loadingOverlay.classList.add('opacity-0', 'pointer-events-none');
        
        // Auto refresh every 10s
        setInterval(() => this.fetchData(true), 10000);
    }

    setupEventListeners() {
        // Drag logic
        this.viewport.addEventListener('mousedown', (e) => {
            this.isDragging = true;
            this.startX = e.clientX - this.translateX;
            this.startY = e.clientY - this.translateY;
            this.viewport.style.cursor = 'grabbing';
        });

        window.addEventListener('mousemove', (e) => {
            if (!this.isDragging) return;
            this.translateX = e.clientX - this.startX;
            this.translateY = e.clientY - this.startY;
            this.applyTransform();
        });

        window.addEventListener('mouseup', () => {
            this.isDragging = false;
            this.viewport.style.cursor = 'grab';
        });

        // Zoom logic (Scroll)
        this.viewport.addEventListener('wheel', (e) => {
            e.preventDefault();
            const zoomSpeed = 0.1;
            const delta = e.deltaY > 0 ? (1 - zoomSpeed) : (1 + zoomSpeed);
            this.applyZoomAt(e.clientX, e.clientY, delta);
        }, { passive: false });

        // Resize handler
        window.addEventListener('resize', () => this.fitToViewport());
    }

    fitToViewport() {
        const vRect = this.viewport.getBoundingClientRect();
        const scaleX = vRect.width / this.baseWidth;
        const scaleY = vRect.height / this.baseHeight;
        this.scale = Math.min(scaleX, scaleY) * 0.9; // 90% of available space
        
        this.wrapper.style.width = this.baseWidth + 'px';
        this.wrapper.style.height = this.baseHeight + 'px';
        
        // Center the map
        this.translateX = (vRect.width - this.baseWidth * this.scale) / 2;
        this.translateY = (vRect.height - this.baseHeight * this.scale) / 2;
        
        this.applyTransform();
    }

    applyTransform() {
        this.wrapper.style.transform = `translate(${this.translateX}px, ${this.translateY}px) scale(${this.scale})`;
    }

    applyZoomAt(clientX, clientY, factor) {
        const rect = this.viewport.getBoundingClientRect();
        const mouseX = clientX - rect.left;
        const mouseY = clientY - rect.top;

        // Current point relative to map origin
        const mapX = (mouseX - this.translateX) / this.scale;
        const mapY = (mouseY - this.translateY) / this.scale;

        const newScale = Math.max(0.2, Math.min(5, this.scale * factor));
        
        // Adjust translation to keep point under mouse
        this.translateX = mouseX - mapX * newScale;
        this.translateY = mouseY - mapY * newScale;
        this.scale = newScale;

        this.applyTransform();
    }

    async fetchData(isSilent = false) {
        try {
            const res = await fetch(`${MAP_DATA_URL}?map_id=${this.mapId}`);
            this.data = await res.json();
            this.renderData();
            this.updateStats();
        } catch (err) {
            console.error('Failed to fetch map data:', err);
        }
    }

    renderData() {
        // Render Slots
        this.slotsLayer.innerHTML = '';
        this.data.slots.forEach(slot => {
            const node = document.createElement('div');
            node.className = `slot-node ${this.activeSlotId == slot.id ? 'active' : ''}`;
            node.dataset.id = slot.id;
            
            // Positioning matches Designer exactly
            node.style.left = slot.x + 'px';
            node.style.top = slot.y + 'px';
            node.style.width = slot.width + 'px';
            node.style.height = slot.height + 'px';
            
            // Status styling
            const colors = this.getSlotColors(slot.status);
            node.style.backgroundColor = colors.bg;
            node.style.borderColor = colors.border;
            node.style.boxShadow = `0 4px 10px ${colors.glow}`;
            
            node.innerHTML = `<span>${slot.code}</span>`;
            
            node.onclick = (e) => {
                e.stopPropagation();
                this.selectSlot(slot);
            };
            
            this.slotsLayer.appendChild(node);
        });

        // Render Cameras
        this.camerasLayer.innerHTML = '';
        this.data.cameras.forEach(cam => {
            const node = document.createElement('div');
            node.className = 'cam-node';
            node.style.left = cam.x + 'px';
            node.style.top = cam.y + 'px';
            node.innerHTML = '<i class="fa-solid fa-video"></i>';
            node.title = cam.nama;
            
            node.onclick = (e) => {
                e.stopPropagation();
                alert(`Opening Camera: ${cam.nama}`);
            };
            
            this.camerasLayer.appendChild(node);
        });
    }

    getSlotColors(status) {
        switch (status) {
            case 'occupied':
                return { bg: 'rgba(244, 63, 94, 0.2)', border: '#f43f5e', glow: 'rgba(244, 63, 94, 0.3)' };
            case 'reserved':
            case 'reserved-by-me':
                return { bg: 'rgba(245, 158, 11, 0.2)', border: '#f59e0b', glow: 'rgba(245, 158, 11, 0.3)' };
            case 'empty':
            default:
                return { bg: 'rgba(16, 185, 129, 0.1)', border: 'rgba(16, 185, 129, 0.5)', glow: 'rgba(16, 185, 129, 0.1)' };
        }
    }

    updateStats() {
        const s = this.data.summary;
        document.getElementById('stat-empty').innerText = s.empty;
        document.getElementById('stat-occupied').innerText = s.occupied;
        
        const percent = Math.round((s.occupied / (s.empty + s.occupied)) * 100) || 0;
        document.getElementById('stat-util-percent').innerText = percent + '%';
        document.getElementById('stat-util-bar').style.width = percent + '%';
    }

    selectSlot(slot) {
        this.activeSlotId = slot.id;
        document.querySelectorAll('.slot-node').forEach(n => n.classList.remove('active'));
        const activeNode = document.querySelector(`.slot-node[data-id="${slot.id}"]`);
        if (activeNode) activeNode.classList.add('active');

        const inspector = document.getElementById('slot-inspector');
        const emptyMsg = document.getElementById('inspector-empty');
        const content = document.getElementById('inspector-content');

        emptyMsg.classList.add('hidden');
        content.classList.remove('hidden');

        document.getElementById('inspect-code').innerText = slot.code;
        
        const statusEl = document.getElementById('inspect-status');
        statusEl.innerText = slot.status;
        statusEl.className = `px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border ${this.getStatusBadgeClass(slot.status)}`;

        document.getElementById('inspect-plate').innerText = slot.vehicle_plate || 'NOT DETECTED';
        document.getElementById('inspect-time').innerText = slot.status === 'occupied' ? 'Session active' : 'Session inactive';
        document.getElementById('inspect-camera').innerText = slot.camera_id ? `Camera #${slot.camera_id}` : 'No direct linkage';

        // Action buttons
        const actions = document.getElementById('inspect-actions');
        actions.innerHTML = '';
        if (slot.status === 'occupied' && slot.transaksi_id) {
            actions.innerHTML = `<a href="/transaksi/${slot.transaksi_id}" class="w-full py-3 bg-emerald-500 text-slate-950 text-[10px] font-black text-center uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-all">View Session</a>`;
        } else if (slot.status === 'empty') {
            actions.innerHTML = `<a href="/check-in?slot_id=${slot.id}" class="w-full py-3 bg-indigo-500 text-slate-950 text-[10px] font-black text-center uppercase tracking-widest rounded-xl hover:bg-indigo-400 transition-all">Manual Check-in</a>`;
        }
    }

    getStatusBadgeClass(status) {
        switch (status) {
            case 'occupied': return 'bg-rose-500/10 text-rose-500 border-rose-500/20';
            case 'reserved': return 'bg-amber-500/10 text-amber-500 border-amber-500/20';
            default: return 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20';
        }
    }
}

// Global engine instance
let mapEngine;
document.addEventListener('DOMContentLoaded', () => {
    mapEngine = new ParkingMapEngine();
});

// Helper functions for inline HTML calls
function zoomMap(factor) { mapEngine.applyZoomAt(window.innerWidth/2, window.innerHeight/2, factor); }
function resetMapZoom() { mapEngine.fitToViewport(); }
function refreshMapData() { mapEngine.fetchData(); }
