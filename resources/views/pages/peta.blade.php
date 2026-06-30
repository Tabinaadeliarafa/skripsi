@extends('layouts.app')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { height: 100%; min-height: 600px; width: 100%; z-index: 10; border-radius: 1rem; }

        /* Custom scrollbar for lists */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.4); }

        .light-scrollbar::-webkit-scrollbar { width: 6px; }
        .light-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .light-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(0, 0, 0, 0.1); border-radius: 10px; }
        .light-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(0, 0, 0, 0.2); }

        @media (max-width: 1023px) {
            #map {
                min-height: 430px;
            }

            #mobile-control-panel {
                height: 100vh;
                max-height: 100vh;
            }

            body.mobile-panel-open {
                overflow: hidden;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Mobile Control Button -->
    <div class="lg:hidden mb-4">
        <button
            type="button"
            onclick="openMobileControlPanel()"
            class="w-full flex items-center justify-center gap-2 bg-primary text-white font-semibold text-sm px-4 py-3 rounded-2xl shadow-md"
        >
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
            Panel Kontrol Peta
        </button>
        </div>

        <!-- Mobile Control Overlay -->
        <div
            id="mobile-control-overlay"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden lg:hidden"
            onclick="closeMobileControlPanel()"
        ></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-auto lg:h-[calc(100vh-120px)] min-h-0 lg:min-h-[700px] items-stretch mt-2 mb-10 relative">

    <!-- Left Panel: Control Panel -->
        <div
            id="mobile-control-panel"
            class="fixed lg:static left-0 top-0 bottom-0 z-50 w-[86vw] max-w-[340px] lg:w-auto lg:max-w-none lg:col-span-3 bg-primary rounded-r-[2rem] lg:rounded-[2rem] text-white flex flex-col overflow-hidden shadow-2xl lg:shadow-lg border border-primary/20 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
        >
        <div class="p-6 pb-4">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-[10px] font-bold text-blue-200 tracking-widest uppercase mb-1">Panel Kontrol</p>
                    <h2 class="text-2xl font-bold text-white">Peta Bencana</h2>
                </div>

                <button
                    type="button"
                    onclick="closeMobileControlPanel()"
                    class="lg:hidden w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
                >
                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                </button>
            </div>

            <!-- Date Filter -->
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-300"></i>
                    <h3 class="text-xs font-semibold text-blue-100 uppercase tracking-wider">Filter Tanggal</h3>
                </div>
                <div class="flex flex-col gap-2">
                    <input type="date" id="filterStartDate" class="w-full text-xs bg-white/10 border-white/20 text-white rounded-lg focus:ring-white focus:border-white px-2 py-2 [color-scheme:dark]">
                    <div class="text-center text-blue-300 text-[10px] uppercase font-bold leading-none">S/D</div>
                    <input type="date" id="filterEndDate" class="w-full text-xs bg-white/10 border-white/20 text-white rounded-lg focus:ring-white focus:border-white px-2 py-2 [color-scheme:dark]">
                </div>
            </div>

            <!-- Jenis Filter -->
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="layers" class="w-4 h-4 text-blue-300"></i>
                    <h3 class="text-xs font-semibold text-blue-100 uppercase tracking-wider">Filter Jenis</h3>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="filter-btn active w-full py-2 px-3 rounded-xl text-xs font-bold transition-all bg-secondary text-white shadow-md border border-secondary" data-type="Semua">Semua</button>
                    <button type="button" class="filter-btn w-full py-2 px-3 rounded-xl text-xs font-medium transition-all bg-white/10 text-blue-100 hover:bg-white/20 border border-white/10" data-type="Banjir">Banjir</button>
                    <button type="button" class="filter-btn w-full py-2 px-3 rounded-xl text-xs font-medium transition-all bg-white/10 text-blue-100 hover:bg-white/20 border border-white/10" data-type="Kekeringan">Kekeringan</button>
                    <button type="button" class="filter-btn w-full py-2 px-3 rounded-xl text-xs font-medium transition-all bg-white/10 text-blue-100 hover:bg-white/20 border border-white/10" data-type="Cuaca Ekstrem">Cuaca Ekstr</button>
                </div>
            </div>

            <!-- Search -->
            <div class="relative mt-2 border-t border-white/10 pt-4">
                <i data-lucide="search" class="w-4 h-4 text-blue-300 absolute left-3 top-[1.6rem]"></i>
                <input type="text" id="searchKecamatan" placeholder="Cari kecamatan..." class="w-full bg-white/10 border border-white/20 text-white text-sm rounded-xl pl-9 pr-4 py-2.5 focus:ring-white focus:border-white placeholder-blue-300/60 transition-all outline-none">
            </div>
        </div>

        <!-- List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar px-2 pb-4" id="kecamatanList">
            <!-- Will be populated by JS -->
        </div>
    </div> <!-- Close Left Panel -->

    <!-- PDF Export Wrapper for Map and Details -->
    <div id="export-area" class="lg:col-span-9 grid grid-cols-1 lg:grid-cols-9 gap-6 h-auto lg:h-full w-full bg-slate-50">
        <!-- Center Panel: Map -->
        <div id="export-map-only" class="lg:col-span-6 bg-brandSurface rounded-[2rem] shadow-sm border border-gray-100 p-2 flex flex-col relative overflow-hidden">
        <div class="flex-1 rounded-[1.5rem] overflow-hidden relative border border-gray-200 shadow-inner">
            <div id="map"></div>
        </div>
        <div class="pt-4 px-5 pb-3 flex flex-col">
            <h4 class="text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Tingkat Risiko (Gabungan):</h4>
            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-gray-600">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Tinggi (&ge; 8 kejadian)</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-400"></span> Sedang (4 - 7 kejadian)</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span> Rendah (< 4 kejadian)</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-200 border border-gray-300"></span> Tidak ada data</div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Details -->
    <div class="lg:col-span-3 flex flex-col gap-4">

        <!-- Detail Wilayah Box -->
        <div class="bg-brandSurface p-6 rounded-[2rem] shadow-sm border border-gray-100 transition-all flex flex-col" id="detailCard">
            <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-4">Detail Wilayah</p>

            <div id="detailEmptyState" class="flex-1 flex flex-col items-center justify-center text-center py-6">
                <i data-lucide="map" class="w-12 h-12 text-gray-200 mb-3"></i>
                <p class="text-sm text-gray-400 font-medium">Pilih kecamatan pada peta</p>
            </div>

            <div id="detailContent" class="hidden cursor-pointer" onclick="if(currentDetailKecamatan) openDrawer(currentDetailKecamatan)">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-bold text-primary" id="detailKecamatan">-</h3>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-gray-300"></i>
                </div>
                <div id="detailBadge" class="mb-5"></div>

                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center gap-2 text-gray-600"><i data-lucide="droplet" class="w-4 h-4 text-blue-500"></i> Banjir</span>
                        <span class="text-primary font-bold" id="detailBanjir">0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center gap-2 text-gray-600"><i data-lucide="thermometer-sun" class="w-4 h-4 text-green-500"></i> Kekeringan</span>
                        <span class="text-primary font-bold" id="detailKekeringan">0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center gap-2 text-gray-600"><i data-lucide="wind" class="w-4 h-4 text-gray-400"></i> Cuaca Ekstrem</span>
                        <span class="text-primary font-bold" id="detailCuaca">0</span>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-4 text-center italic">*Klik area ini untuk melihat rincian laporan</p>
            </div>
        </div>

        <!-- Ranking Box -->
        <div class="bg-brandSurface p-6 rounded-[2rem] shadow-sm border border-gray-100 flex-1 flex flex-col min-h-[250px]">
            <h3 class="text-sm font-bold text-primary mb-4" id="rankingTitle">Ranking Risiko — Semua</h3>
            <div class="flex-1 overflow-y-auto light-scrollbar pr-2 space-y-3" id="rankingList">
                <!-- Injected via JS -->
            </div>
        </div>

        <!-- Export Buttons -->
        <div id="export-card" class="grid grid-cols-1 gap-3 mt-auto">
            <button onclick="exportCSV()" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-5 py-3.5 rounded-2xl font-semibold shadow-sm transition-colors flex items-center justify-between">
                <span>Export CSV</span>
                <i data-lucide="download" class="w-5 h-5 text-gray-400"></i>
            </button>
            <button onclick="exportToPDF()" class="bg-primary hover:opacity-90 text-white px-5 py-3.5 rounded-2xl font-semibold shadow-md transition-opacity flex items-center justify-between">
                <span>Export PDF</span>
                <i data-lucide="file-text" class="w-5 h-5 text-blue-300"></i>
            </button>
        </div>

    </div>
    <!-- End PDF Export Wrapper -->
</div>

<!-- Drawer Overlay -->
<div id="drawer-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300" onclick="closeDrawer()"></div>

<!-- Drawer Panel -->
<div id="report-drawer" class="fixed right-0 top-0 h-full w-full sm:w-[400px] bg-brandSurface shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
        <div>
            <h2 class="text-xl font-bold text-primary" id="drawer-title">Detail Kecamatan</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium" id="drawer-subtitle">0 Laporan Bencana</p>
        </div>
        <button onclick="closeDrawer()" class="p-2.5 text-gray-400 hover:text-gray-800 rounded-full hover:bg-gray-100 transition-colors bg-gray-50">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <div class="p-6 overflow-y-auto flex-1 bg-brandBg" id="drawer-content">
        <!-- Laporan cards will be injected here via JS -->
    </div>
</div>

<!-- Print Data Container (Hidden by default, appended to map during export) -->
<div id="print-data-container" class="hidden grid-cols-2 gap-8 bg-white pt-6 mt-4 w-full text-gray-800">
    <div>
        <h3 class="text-lg font-bold text-primary mb-3 border-b border-gray-200 pb-2">Ranking Risiko (Top 5) — <span id="print-filter-type">Semua</span></h3>
        <table class="w-full text-sm text-left border border-gray-100 rounded-lg overflow-hidden shadow-sm">
            <thead class="bg-slate-50 text-gray-600 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-2 w-12 text-center">No</th>
                    <th class="px-4 py-2">Kecamatan</th>
                    <th class="px-4 py-2 text-center">Total Kejadian</th>
                </tr>
            </thead>
            <tbody id="print-ranking-body" class="divide-y divide-gray-100">
                <!-- Injected via JS -->
            </tbody>
        </table>
    </div>
    <div>
        <h3 class="text-lg font-bold text-primary mb-3 border-b border-gray-200 pb-2">Detail Wilayah: <span id="print-detail-title" class="text-gray-500 font-medium">(Pilih kecamatan pada peta)</span></h3>
        <table class="w-full text-sm text-left border border-gray-100 rounded-lg overflow-hidden shadow-sm">
            <thead class="bg-slate-50 text-gray-600 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-2 text-center text-blue-600">Banjir</th>
                    <th class="px-4 py-2 text-center text-green-600">Kekeringan</th>
                    <th class="px-4 py-2 text-center text-orange-600">Cuaca Ekstrem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="bg-white">
                    <td class="px-4 py-3 text-center font-bold text-lg text-gray-800" id="print-detail-banjir">-</td>
                    <td class="px-4 py-3 text-center font-bold text-lg text-gray-800" id="print-detail-kekeringan">-</td>
                    <td class="px-4 py-3 text-center font-bold text-lg text-gray-800" id="print-detail-cuaca">-</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    var laporans = @json($laporans);
    var kecamatansData = @json($kecamatans);

    // Global States
    var currentFilterType = 'Semua';
    var currentDateStart = '';
    var currentDateEnd = '';
    var currentSearch = '';
    var currentDetailKecamatan = null;

    var kecStats = {};
    var map;
    var geojsonLayer;

    // Elements
    var listEl = document.getElementById('kecamatanList');
    var rankingEl = document.getElementById('rankingList');
    var filterBtns = document.querySelectorAll('.filter-btn');
    var searchInput = document.getElementById('searchKecamatan');
    var sdInput = document.getElementById('filterStartDate');
    var edInput = document.getElementById('filterEndDate');

    function openMobileControlPanel() {
        var panel = document.getElementById('mobile-control-panel');
        var overlay = document.getElementById('mobile-control-overlay');

        if (!panel || !overlay) return;

        panel.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('mobile-panel-open');

        // setTimeout(function() {
        //     map.invalidateSize();
        // }, 300);
    }

    function closeMobileControlPanel() {
        var panel = document.getElementById('mobile-control-panel');
        var overlay = document.getElementById('mobile-control-overlay');

        if (!panel || !overlay) return;

        panel.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('mobile-panel-open');

        // setTimeout(function() {
        //     map.invalidateSize();
        // }, 300);
    }

    // Initialize Map
    map = L.map('map', { scrollWheelZoom: false, zoomControl: false, preferCanvas: true }).setView([-6.238270, 106.975571], 10);
    L.control.zoom({ position: 'topleft' }).addTo(map);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Initial load
    fetch('/geo/kecamatan.geojson')
        .then(res => res.json())
        .then(data => {
            geojsonLayer = L.geoJSON(data, {
                style: getFeatureStyle,
                onEachFeature: bindFeatureEvents
            }).addTo(map);

            // Set initial state from existing URL params if any
            var urlParams = new URLSearchParams(window.location.search);
            if(urlParams.get('start_date')) { sdInput.value = urlParams.get('start_date'); currentDateStart = sdInput.value; }
            if(urlParams.get('end_date')) { edInput.value = urlParams.get('end_date'); currentDateEnd = edInput.value; }

            refreshData();
            map.fitBounds(geojsonLayer.getBounds());
        });

    function refreshData() {
        // Reset stats
        kecamatansData.forEach(function(k) {
            kecStats[k.name.toUpperCase()] = { name: k.name, total: 0, banjir: 0, kekeringan: 0, cuaca: 0 };
        });

        // Calculate based on filters
        laporans.forEach(function(l) {
            // Check Dates
            if (currentDateStart && new Date(l.date) < new Date(currentDateStart)) return;
            if (currentDateEnd && new Date(l.date) > new Date(currentDateEnd)) return;

            // Check Type Filter
            var typeName = l.jenis_bencana ? l.jenis_bencana.name : '';
            var typeMatches = false;
            if (currentFilterType === 'Semua') typeMatches = true;
            else if (currentFilterType === 'Banjir' && typeName === 'Banjir') typeMatches = true;
            else if (currentFilterType === 'Kekeringan' && typeName === 'Kekeringan') typeMatches = true;
            else if (currentFilterType === 'Cuaca Ekstrem' && (typeName === 'Cuaca Ekstrem' || typeName === 'Angin Puting Beliung')) typeMatches = true;

            if (!typeMatches) return;

            // Increment
            if (l.desa && l.desa.kecamatan && l.desa.kecamatan.name) {
                var kecName = l.desa.kecamatan.name.toUpperCase();
                if (kecStats[kecName] !== undefined) {
                    kecStats[kecName].total++;
                    if (typeName === 'Banjir') kecStats[kecName].banjir++;
                    else if (typeName === 'Kekeringan') kecStats[kecName].kekeringan++;
                    else if (typeName === 'Cuaca Ekstrem' || typeName === 'Angin Puting Beliung') kecStats[kecName].cuaca++;
                }
            }
        });

        updateMap();
        updateLeftList();
        updateRanking();

        // Refresh detail card if one is selected
        if(currentDetailKecamatan) {
            showDetailCard(currentDetailKecamatan);
        }
    }

    function getFeatureStyle(feature) {
        var kecName = feature.properties.NAME_3 ? feature.properties.NAME_3.toUpperCase() : '';
        var count = kecStats[kecName] ? kecStats[kecName].total : 0;

        var fillColor = '#e5e7eb'; // Tidak ada data
        if (count >= 1 && count <= 3) fillColor = '#22c55e'; // Rendah
        if (count >= 4 && count <= 7) fillColor = '#fb923c'; // Sedang
        if (count >= 8) fillColor = '#ef4444'; // Tinggi

        return { color: 'white', weight: 1.5, fillColor: fillColor, fillOpacity: 0.85 };
    }

    function bindFeatureEvents(feature, layer) {
        if (feature.properties && feature.properties.NAME_3) {
            var kecName = feature.properties.NAME_3;
            layer.on('click', function(e) {
                showDetailCard(kecName);

                // Highlight polygon
                geojsonLayer.resetStyle();
                e.target.setStyle({ weight: 3, color: '#12395C', fillOpacity: 1 });
                e.target.bringToFront();
            });

            layer.on('dblclick', function(e) {
                openDrawer(kecName);
            });

            // Hover effect
            layer.on('mouseover', function(e) {
                var currentWeight = e.target.options.weight;
                if(currentWeight !== 3) {
                    e.target.setStyle({ fillOpacity: 1 });
                }
            });
            layer.on('mouseout', function(e) {
                var currentWeight = e.target.options.weight;
                if(currentWeight !== 3) {
                    geojsonLayer.resetStyle(e.target);
                }
            });
        }
    }

    function updateMap() {
        if (geojsonLayer) geojsonLayer.setStyle(getFeatureStyle);
    }

    function updateLeftList() {
        var html = '';
        var arr = Object.values(kecStats);

        // Filter by Search
        if (currentSearch) {
            arr = arr.filter(k => k.name.toLowerCase().includes(currentSearch.toLowerCase()));
        }

        // Sort alphabetically
        arr.sort((a, b) => a.name.localeCompare(b.name));

        if (arr.length === 0) {
            html = '<div class="px-4 py-3 text-sm text-blue-200">Tidak ada kecamatan cocok.</div>';
        } else {
            arr.forEach(k => {
                html += `
                <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5 cursor-pointer transition-colors flex justify-between items-center group" onclick="focusKecamatan('${k.name}')">
                    <span class="text-sm font-medium text-blue-50 group-hover:text-white transition-colors">${k.name}</span>
                    <span class="text-xs font-bold text-blue-200 bg-white/10 px-2 py-0.5 rounded">${k.total}</span>
                </div>`;
            });
        }
        listEl.innerHTML = html;
    }

    function updateRanking() {
        document.getElementById('rankingTitle').innerText = 'Ranking Risiko — ' + currentFilterType;
        var arr = Object.values(kecStats).sort((a, b) => b.total - a.total).slice(0, 5); // Top 5
        var html = '';

        arr.forEach((k, index) => {
            var count = k.total;
            var badge = '';
            if (count >= 8) badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600">Tinggi</span>`;
            else if (count >= 4) badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-600">Sedang</span>`;
            else if (count >= 1) badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-600">Rendah</span>`;
            else badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">Aman</span>`;

            html += `
            <div class="flex items-center justify-between py-1 border-b border-gray-50 last:border-0 cursor-pointer hover:bg-gray-50 transition-colors" onclick="focusKecamatan('${k.name}')">
                <div class="flex items-center gap-3 px-1 py-1">
                    <span class="text-xs font-bold text-gray-400 w-3">${index + 1}</span>
                    <span class="text-sm font-semibold text-gray-700">${k.name}</span>
                </div>
                ${badge}
            </div>`;
        });

        rankingEl.innerHTML = html;
    }

    function showDetailCard(kecName) {
        currentDetailKecamatan = kecName;
        document.getElementById('detailEmptyState').classList.add('hidden');
        document.getElementById('detailContent').classList.remove('hidden');

        var nameUpper = kecName.toUpperCase();
        var stats = kecStats[nameUpper];

        document.getElementById('detailKecamatan').innerText = kecName;
        document.getElementById('detailBanjir').innerText = stats ? stats.banjir : 0;
        document.getElementById('detailKekeringan').innerText = stats ? stats.kekeringan : 0;
        document.getElementById('detailCuaca').innerText = stats ? stats.cuaca : 0;

        var total = stats ? stats.total : 0;
        var badgeHtml = '';
        if (total >= 8) badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Tinggi</span>`;
        else if (total >= 4) badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Sedang</span>`;
        else if (total >= 1) badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Rendah</span>`;
        else badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Aman</span>`;

        document.getElementById('detailBadge').innerHTML = badgeHtml;
    }

    function focusKecamatan(kecName) {
        if (!geojsonLayer) return;
        geojsonLayer.eachLayer(function(layer) {
            if (layer.feature.properties && layer.feature.properties.NAME_3 === kecName) {
                // Simulasikan klik
                layer.fire('click');
                // Pan map
                map.panTo(layer.getBounds().getCenter());
                if (window.innerWidth < 1024) {
                    closeMobileControlPanel();
                }
            }
        });
    }

    // --- EVENT LISTENERS --- //

    // Filter Buttons
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Reset active styles
            filterBtns.forEach(b => {
                b.classList.remove('bg-secondary', 'text-white', 'border-secondary', 'font-bold', 'shadow-md');
                b.classList.add('bg-white/10', 'text-blue-100', 'border-white/10', 'font-medium');
            });
            // Set active style
            this.classList.remove('bg-white/10', 'text-blue-100', 'border-white/10', 'font-medium');
            this.classList.add('bg-secondary', 'text-white', 'border-secondary', 'font-bold', 'shadow-md');

            currentFilterType = this.getAttribute('data-type');
            refreshData();
            
            if (window.innerWidth < 1024) {
                closeMobileControlPanel();
            }
        });
    });

    // Date Inputs
    sdInput.addEventListener('change', function() { currentDateStart = this.value; refreshData(); });
    edInput.addEventListener('change', function() { currentDateEnd = this.value; refreshData(); });

    // Search Input
    searchInput.addEventListener('input', function() { currentSearch = this.value; updateLeftList(); });

    // --- EXPORT ---
    function exportToPDF() {
        var element = document.getElementById('export-map-only');
        var exportCard = document.getElementById('export-card');
        var printData = document.getElementById('print-data-container');
        
        if(exportCard) exportCard.style.display = 'none';

        // 1. Populate Ranking Data
        var arr = Object.values(kecStats).sort((a,b) => b.total - a.total).slice(0, 5);
        var rankingHtml = '';
        arr.forEach(function(k, index) {
            rankingHtml += '<tr class="bg-white"><td class="px-4 py-2 text-center border-b border-gray-50">' + (index+1) + '</td><td class="px-4 py-2 font-semibold text-gray-800 border-b border-gray-50">' + k.name + '</td><td class="px-4 py-2 text-center font-bold text-primary border-b border-gray-50">' + k.total + '</td></tr>';
        });
        document.getElementById('print-ranking-body').innerHTML = rankingHtml;
        document.getElementById('print-filter-type').innerText = currentFilterType;

        // 2. Populate Detail Data
        if (currentDetailKecamatan && kecStats[currentDetailKecamatan]) {
            var detail = kecStats[currentDetailKecamatan];
            document.getElementById('print-detail-title').innerText = currentDetailKecamatan;
            document.getElementById('print-detail-banjir').innerText = detail.banjir;
            document.getElementById('print-detail-kekeringan').innerText = detail.kekeringan;
            document.getElementById('print-detail-cuaca').innerText = detail.cuaca;
        } else {
            document.getElementById('print-detail-title').innerText = '(Semua Wilayah)';
            document.getElementById('print-detail-banjir').innerText = '-';
            document.getElementById('print-detail-kekeringan').innerText = '-';
            document.getElementById('print-detail-cuaca').innerText = '-';
        }

        // 3. Pasang Print Container ke dalam map agar ikut ter-screenshot
        printData.classList.remove('hidden');
        printData.classList.add('grid');
        element.appendChild(printData);

        // Kunci ukuran elemen dengan pixel pasti agar CSS responsive tidak merusak Leaflet saat clone
        var originalWidth = element.style.width;
        var originalHeight = element.style.height;
        element.style.width = element.offsetWidth + 'px';
        element.style.height = element.offsetHeight + 'px';

        var overlay = document.createElement('div');
        overlay.id = 'pdf-loading-overlay';
        overlay.className = 'fixed inset-0 bg-white/90 z-[9999] flex flex-col items-center justify-center';
        overlay.innerHTML = '<i data-lucide="loader" class="w-12 h-12 text-primary animate-spin mb-4"></i><h2 class="text-xl font-bold text-gray-800">Menyusun Laporan PDF...</h2>';
        document.body.appendChild(overlay);
        lucide.createIcons();

        var opt = {
            margin:       0.3,
            filename:     'Peta_Risiko_Bencana_Kab_Bekasi.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true,
                windowWidth: window.innerWidth,
                windowHeight: window.innerHeight
            },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        setTimeout(function() {
            html2pdf().set(opt).from(element).save().then(function() {
                element.style.width = originalWidth;
                element.style.height = originalHeight;
                if(exportCard) exportCard.style.display = 'flex';
                document.body.removeChild(overlay);
                
                // Kembalikan print container ke luar
                printData.classList.add('hidden');
                printData.classList.remove('grid');
                document.body.appendChild(printData);
            }).catch(function(err) {
                console.error("PDF Export error: ", err);
                element.style.width = originalWidth;
                element.style.height = originalHeight;
                document.body.removeChild(overlay);
                if(exportCard) exportCard.style.display = 'flex';
                
                printData.classList.add('hidden');
                printData.classList.remove('grid');
                document.body.appendChild(printData);
            });
        }, 100);
    }

    function exportCSV() {
        var csv = 'Kecamatan,Total Kejadian,Banjir,Kekeringan,Cuaca Ekstrem\n';
        var arr = Object.values(kecStats).sort((a,b) => a.name.localeCompare(b.name));
        arr.forEach(k => {
            csv += `"${k.name}",${k.total},${k.banjir},${k.kekeringan},${k.cuaca}\n`;
        });

        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement("a");
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "Rekap_Risiko_Kecamatan.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // --- DRAWER --- //
    function openDrawer(kecamatanName) {
        document.getElementById('drawer-overlay').classList.remove('hidden');
        document.getElementById('report-drawer').classList.remove('translate-x-full');
        document.getElementById('drawer-title').innerText = 'Kec. ' + kecamatanName;

        var filteredLaporans = laporans.filter(function(l) {
            // Match Area
            var matchKecamatan = l.desa && l.desa.kecamatan && l.desa.kecamatan.name.toUpperCase() === kecamatanName.toUpperCase();
            if(!matchKecamatan) return false;

            // Match Dates
            if (currentDateStart && new Date(l.date) < new Date(currentDateStart)) return false;
            if (currentDateEnd && new Date(l.date) > new Date(currentDateEnd)) return false;

            // Match Type Filter
            var typeName = l.jenis_bencana ? l.jenis_bencana.name : '';
            if (currentFilterType !== 'Semua') {
                if (currentFilterType === 'Cuaca Ekstrem') {
                    if (typeName !== 'Cuaca Ekstrem' && typeName !== 'Angin Puting Beliung') return false;
                } else {
                    if (typeName !== currentFilterType) return false;
                }
            }
            return true;
        });

        document.getElementById('drawer-subtitle').innerText = filteredLaporans.length + ' Laporan Bencana';

        var contentHtml = '';
        if(filteredLaporans.length === 0) {
            contentHtml = `<div class="text-center py-10 text-gray-500 flex flex-col items-center">
                <i data-lucide="shield-check" class="w-12 h-12 mb-3 text-green-500"></i>
                <p class="text-sm">Tidak ada laporan sesuai filter saat ini.</p>
            </div>`;
        } else {
            filteredLaporans.forEach(function(l) {
                var statusBadge = l.status === 'active'
                    ? `<span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[10px] uppercase font-bold tracking-wider border border-red-200">Aktif</span>`
                    : `<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] uppercase font-bold tracking-wider border border-green-200">Selesai</span>`;

                contentHtml += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-3 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <h4 class="font-bold text-gray-900 text-sm leading-tight flex-1">${l.title}</h4>
                            <div class="flex-shrink-0 mt-0.5">${statusBadge}</div>
                        </div>
                        <div class="space-y-1.5 mb-3">
                            <p class="text-xs text-gray-600 flex items-center gap-2"><span class="bg-gray-100 p-1 rounded"><i data-lucide="tag" class="w-3 h-3 text-gray-500"></i></span> ${(l.jenis_bencana ? l.jenis_bencana.name : '-')}</p>
                            <p class="text-xs text-gray-600 flex items-center gap-2"><span class="bg-gray-100 p-1 rounded"><i data-lucide="map-pin" class="w-3 h-3 text-gray-500"></i></span> Desa ${(l.desa ? l.desa.name : '-')}</p>
                            <p class="text-xs text-gray-600 flex items-center gap-2"><span class="bg-gray-100 p-1 rounded"><i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i></span> ${new Date(l.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                        </div>
                    </div>`;
            });
        }
        document.getElementById('drawer-content').innerHTML = contentHtml;
        lucide.createIcons();
    }

    function closeDrawer() {
        document.getElementById('drawer-overlay').classList.add('hidden');
        document.getElementById('report-drawer').classList.add('translate-x-full');
    }

    map.on('focus', function() { map.scrollWheelZoom.enable(); });
    map.on('blur', function() { map.scrollWheelZoom.disable(); });
</script>
@endpush
