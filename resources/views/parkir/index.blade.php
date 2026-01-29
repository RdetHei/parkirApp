@extends('layouts.app')

@section('title', 'Parkir Masuk')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Kendaraan Parkir Aktif</h2>
        <a href="{{ route('transaksi.create-check-in') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Tambah Parkir
        </a>
    </div>

    @if($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mx-4 sm:mx-6 lg:mx-8">
            {{ $message }}
        </div>
    @endif

    <div id="parking-map-container" class="bg-white shadow-lg rounded-lg p-4">
        <!-- SVG parking map will be rendered here by JavaScript -->
        <h3 class="text-center text-gray-500 text-lg">Memuat peta parkir...</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total User</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400 mt-2">Admin: 0 • Petugas: 0</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Aktif</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Kendaraan</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400 mt-2">Motor: 0 • Mobil: 0</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">0% Full</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Area Parkir</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400 mt-2">Kapasitas: 0 • Terisi: 0</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">Today</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp 0</p>
                        <p class="text-xs text-gray-400 mt-2">0 transaksi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">Kendaraan Parkir</h2>
                            <button class="text-sm font-semibold text-green-600">Lihat Semua</button>
                        </div>
                        <div class="p-6 text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <p class="text-gray-900 font-semibold mb-1">Tidak Ada Kendaraan</p>
                            <p class="text-sm text-gray-500">Belum ada kendaraan parkir</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl">Tambah User</button>
                            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200">Daftar Kendaraan</button>
                            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200">Kelola Area</button>
                            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200">Atur Tarif</button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <div class="bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">Log Aktivitas</h2>
                            <button class="text-sm font-semibold text-green-600">Lihat Semua</button>
                        </div>
                        <div class="p-6 text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-900 font-semibold mb-1">Belum Ada Aktivitas</p>
                            <p class="text-sm text-gray-500">Log aktivitas kosong</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">Tarif Parkir</h2>
                            <button class="text-sm font-semibold text-green-600">Edit</button>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">Motor</p>
                                    <p class="text-xs text-gray-500">Per jam</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">-</p>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">Mobil</p>
                                    <p class="text-xs text-gray-500">Per jam</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">-</p>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">Lainnya</p>
                                    <p class="text-xs text-gray-500">Per jam</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const parkingMapContainer = document.getElementById('parking-map-container');
        const API_URL = '{{ route('api.parking-slots') }}';
        let parkingData = []; // To store fetched parking slot data

        // SVG path for a generic car icon (simplified)
        const CAR_ICON_SVG = '<path d="M17.485 5.176l-.88 1.77C15.686 9.697 13.9 11 12 11s-3.686-1.303-4.605-4.054l-.88-1.77C5.394 4.542 4 4.808 4 6.304V18c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6.304c0-1.496-1.394-1.762-2.515-1.128zM12 17c-1.38 0-2.5-1.12-2.5-2.5S10.62 12 12 12s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>';

        // SVG path for a generic motorcycle icon (simplified)
        const MOTORCYCLE_ICON_SVG = '<path d="M19 11.5L16.25 10l-.45-1.07c-.43-1.02-.92-2.19-2.02-2.42-1.09-.23-2.09.43-2.88 1.1l-1.92 1.94c-.45-.25-.97-.39-1.5-.39-.97 0-1.85.39-2.5 1.02-.65-.63-1.53-1.02-2.5-1.02-.53 0-1.05.14-1.5.39l-1.92-1.94c-.79-.67-1.79-1.33-2.88-1.1-1.1.23-1.59 1.4-2.02 2.42l-.45 1.07L5 11.5c.6.28 1.04.75 1.3 1.3.26.55.39 1.16.39 1.8 0 .64-.13 1.25-.39 1.8-.26.55-.7 1.02-1.3 1.3L3.02 18.5l-.45 1.07c-.43 1.02-.92 2.19-2.02 2.42-1.09.23-2.09-.43-2.88-1.1l-1.92-1.94c-.45-.25-.97-.39-1.5-.39-.97 0-1.85.39-2.5 1.02-.65-.63-1.53-1.02-2.5-1.02-.53 0-1.05.14-1.5.39L-.02 18.5l-.45 1.07c-.43 1.02-.92 2.19-2.02 2.42-1.09.23-2.09-.43-2.88-1.1l-1.92-1.94c-.79-.67-1.79-1.33-2.88-1.1-1.1.23-1.59 1.4-2.02 2.42l-.45 1.07L1 11.5c.6-.28 1.04.75 1.3-1.3.26.55.39 1.16.39 1.8 0-.64-.13 1.25-.39 1.8-.26.55-.7-1.02-1.3-1.3zm12-5.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM6 13a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm-3 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm-3 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>';

        const svgVehicleIcons = {
            'mobil': CAR_ICON_SVG,
            'motor': MOTORCYCLE_ICON_SVG,
            'lainnya': CAR_ICON_SVG, // Default to car for 'lainnya'
        };

        // Static layout for parking spots (for demonstration)
        // In a real app, these would come from the database or a configuration.
        // Each entry corresponds to an AreaParkir.id
        const layoutConfig = {
            1: { x: 10, y: 10, width: 80, height: 40 }, // Area ID 1
            2: { x: 100, y: 10, width: 80, height: 40 }, // Area ID 2
            3: { x: 10, y: 60, width: 80, height: 40 },  // Area ID 3
            4: { x: 100, y: 60, width: 80, height: 40 }, // Area ID 4
            // ... add more as needed. You might need to retrieve actual AreaParkir IDs and map them.
        };

        async function fetchParkingData() {
            try {
                const response = await fetch(API_URL);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                parkingData = data;
                renderParkingMap();
            } catch (error) {
                console.error("Error fetching parking data:", error);
                parkingMapContainer.innerHTML = `<h3 class="text-center text-red-500 text-lg">Gagal memuat peta parkir.</h3>`;
            }
        }

        function renderParkingMap() {
            if (parkingData.length === 0) {
                parkingMapContainer.innerHTML = `<h3 class="text-center text-gray-500 text-lg">Tidak ada area parkir tersedia.</h3>`;
                return;
            }

            // Determine SVG dimensions based on layoutConfig
            let maxX = 0;
            let maxY = 0;
            // Iterate through layoutConfig and available parking areas to determine dimensions
            parkingData.forEach(area => {
                const config = layoutConfig[area.id];
                if (config) {
                    maxX = Math.max(maxX, config.x + config.width);
                    maxY = Math.max(maxY, config.y + config.height);
                }
            });
            
            const svgWidth = maxX > 0 ? maxX + 20 : 300; // Default if no areas, or add padding
            const svgHeight = maxY > 0 ? maxY + 20 : 200; // Default if no areas, or add padding

            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.setAttribute("viewBox", `0 0 ${svgWidth} ${svgHeight}`);
            svg.setAttribute("width", "100%");
            svg.setAttribute("height", "auto");
            svg.classList.add("parking-map-svg"); // Add a class for CSS styling

            parkingMapContainer.innerHTML = ''; // Clear loading message
            parkingMapContainer.appendChild(svg);

            parkingData.forEach(area => {
                const config = layoutConfig[area.id];
                if (!config) {
                    console.warn(`Layout configuration missing for Area ID: ${area.id}. Skipping.`);
                    return;
                }

                const g = document.createElementNS("http://www.w3.org/2000/svg", "g"); // Group for slot elements
                g.setAttribute("data-id", area.id);
                g.setAttribute("data-name", area.name);
                g.setAttribute("data-status", area.status);
                g.classList.add("parking-slot-group");
                svg.appendChild(g);

                const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
                rect.setAttribute("x", config.x);
                rect.setAttribute("y", config.y);
                rect.setAttribute("width", config.width);
                rect.setAttribute("height", config.height);
                rect.setAttribute("rx", 5); // Rounded corners
                rect.setAttribute("ry", 5);
                rect.setAttribute("stroke", "#6B7280"); // Gray stroke
                rect.setAttribute("stroke-width", 1);
                rect.classList.add("parking-slot-rect");
                g.appendChild(rect);

                // Set color based on status
                let fillColor = "#10B981"; // Green (empty)
                let textColor = "#374151"; // Dark gray
                if (area.status === 'occupied') {
                    fillColor = "#EF4444"; // Red
                    textColor = "#FFFFFF"; // White
                } else if (area.status === 'bookmarked') { // Not implemented yet, but for future
                    fillColor = "#F59E0B"; // Yellow
                    textColor = "#FFFFFF"; // White
                }
                rect.setAttribute("fill", fillColor);

                // Add area name label
                const nameText = document.createElementNS("http://www.w3.org/2000/svg", "text");
                nameText.setAttribute("x", config.x + config.width / 2);
                nameText.setAttribute("y", config.y + config.height / 2 + (area.status === 'occupied' ? -15 : 0)); // Adjust position if vehicle icon present
                nameText.setAttribute("text-anchor", "middle");
                nameText.setAttribute("fill", textColor);
                nameText.setAttribute("font-size", "12");
                nameText.setAttribute("font-weight", "bold");
                nameText.textContent = area.name;
                g.appendChild(nameText);

                // Add vehicle icon and plate number if occupied
                if (area.status === 'occupied' && area.vehicle) {
                    const iconSize = 24; // SVG icon intrinsic size is often 24x24
                    const iconScale = 1; // Scale factor
                    const iconTranslateX = config.x + config.width / 2 - (iconSize / 2 * iconScale);
                    const iconTranslateY = config.y + config.height / 2 - (iconSize / 2 * iconScale) + (nameText ? 5 : 0); // Adjust Y if name is above

                    const vehicleIconSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                    vehicleIconSvg.setAttribute("x", iconTranslateX);
                    vehicleIconSvg.setAttribute("y", iconTranslateY);
                    vehicleIconSvg.setAttribute("width", iconSize * iconScale);
                    vehicleIconSvg.setAttribute("height", iconSize * iconScale);
                    vehicleIconSvg.setAttribute("fill", "#FFFFFF"); // White icon
                    vehicleIconSvg.innerHTML = svgVehicleIcons[area.vehicle.jenis_kendaraan.toLowerCase()] || svgVehicleIcons['mobil']; // Use specific or default
                    vehicleIconSvg.classList.add("vehicle-icon");
                    g.appendChild(vehicleIconSvg);
                    
                    const plateText = document.createElementNS("http://www.w3.org/2000/svg", "text");
                    plateText.setAttribute("x", config.x + config.width / 2);
                    plateText.setAttribute("y", config.y + config.height / 2 + 25); // Below the icon
                    plateText.setAttribute("text-anchor", "middle");
                    plateText.setAttribute("fill", "#FFFFFF");
                    plateText.setAttribute("font-size", "10");
                    plateText.textContent = area.vehicle.plat_nomor;
                    g.appendChild(plateText);
                }

                // Add click event listener to the group
                g.addEventListener('click', () => handleSlotClick(area.id, area.status, area));
            });
        }

        function handleSlotClick(areaId, currentStatus, areaData) {
            alert(`Clicked on Area: ${areaData.name}, Status: ${currentStatus}`);
            // This is where interaction logic would go:
            // - If empty: Offer to bookmark or check-in
            // - If bookmarked: Offer to check-in or unbookmark
            // - If occupied: Show vehicle details, offer check-out
            
            // Example: Change status (for testing, not persistent yet)
            // This would typically involve an AJAX call to update the backend
            // For now, let's just re-fetch and re-render (which would reset changes)
            // For actual interaction, you'd need modals/forms.
        }

        // Initial fetch and render
        fetchParkingData();

        // Optional: Set up polling for real-time updates (e.g., every 5 seconds)
        setInterval(fetchParkingData, 5000); 
    });
</script>
@endpush

<style>
    .parking-map-svg {
        border: 1px solid #E5E7EB; /* light gray border */
        border-radius: 0.5rem;
        background-color: #F9FAFB; /* very light gray background */
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .parking-slot-group {
        cursor: pointer;
    }
    .parking-slot-group:hover .parking-slot-rect {
        stroke-width: 2;
        filter: brightness(1.05);
    }
    .parking-slot-rect {
        transition: fill 0.3s ease, stroke 0.3s ease, stroke-width 0.3s ease;
    }
    /* Add specific styles for vehicle icons if needed */
    .vehicle-icon {
        pointer-events: none; /* Make icons unclickable, clicks pass through to rect */
    }
</style>
