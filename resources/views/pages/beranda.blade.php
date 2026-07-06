@extends('layouts.app')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { height: 100%; min-height: 400px; width: 100%; z-index: 10; border-radius: 1rem; }

        /* Custom range slider styling */
        input[type=range] {
            -webkit-appearance: none;
            width: 100%;
            background: transparent;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #D45B1F;
            cursor: pointer;
            margin-top: -6px;
            border: 4px solid #F2EFEB;
            box-shadow: 0 0 0 2px #D45B1F;
        }
        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 8px;
            cursor: pointer;
            background: #D45B1F;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
<div class="flex flex-col gap-10 mt-4 mb-10">

    <!-- Hero Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <!-- Left: Text -->
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brandSurface border border-gray-200 text-xs font-medium text-gray-600 mb-6 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                Data BPBD Kabupaten Bekasi - {{ date('Y') }}
            </div>

            <h1 class="text-5xl md:text-6xl font-extrabold text-primary mb-4 tracking-tight leading-tight">
                Bencana Alam <span class="text-secondary">Kab.<br>Bekasi</span>
            </h1>

            <p class="text-gray-600 text-lg mb-8 max-w-lg leading-relaxed">
                Website pemantauan bencana Kabupaten Bekasi yang menampilkan data, peta interaktif, dan prediksi risiko bencana secara informatif.
            </p>

            <a href="/peta" class="inline-flex items-center gap-2 bg-primary hover:opacity-90 text-white px-6 py-3 rounded-full font-medium transition-opacity shadow-md">
                <i data-lucide="map-pin" class="w-5 h-5"></i> Lihat Peta Interaktif &rarr;
            </a>
        </div>

        <!-- Right: Risiko Tertinggi Card -->
        <div class="flex justify-end">
            <div class="bg-brandSurface p-8 rounded-[2rem] shadow-sm border border-gray-100 w-full mx-auto w-full max-w-sm sm:max-w-none">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-xs font-bold text-gray-500 tracking-widest uppercase mb-1">Risiko Tertinggi</p>
                        <h2 class="text-3xl font-bold text-primary">{{ $kecamatanRisikoTinggi ? $kecamatanRisikoTinggi->name : 'Aman' }}</h2>
                    </div>
                    @if($kecamatanRisikoTinggi && $kecamatanRisikoTinggi->laporan_bencanas_count >= 8)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Tinggi
                        </span>
                    @elseif($kecamatanRisikoTinggi && $kecamatanRisikoTinggi->laporan_bencanas_count >= 4)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Sedang
                        </span>
                    @elseif($kecamatanRisikoTinggi && $kecamatanRisikoTinggi->laporan_bencanas_count >= 1)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Rendah
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Aman
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-4 border-t border-gray-100 pt-6">
                    <div>
                        <div class="text-3xl font-bold text-primary mb-1">{{ isset($breakdownKejadian['Banjir']) ? $breakdownKejadian['Banjir'] : 0 }}</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Banjir</div>
                    </div>
                    <div class="border-l border-gray-100 pl-4">
                        <div class="text-3xl font-bold text-primary mb-1">{{ isset($breakdownKejadian['Kekeringan']) ? $breakdownKejadian['Kekeringan'] : 0 }}</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Kekeringan</div>
                    </div>
                    <div class="border-l border-gray-100 pl-4">
                        <div class="text-3xl font-bold text-primary mb-1">{{ isset($breakdownKejadian['Cuaca Ekstrem']) ? $breakdownKejadian['Cuaca Ekstrem'] : (isset($breakdownKejadian['Angin Puting Beliung']) ? $breakdownKejadian['Angin Puting Beliung'] : 0) }}</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Cuaca Ekstrem</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistic Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Kecamatan -->
        <div class="bg-brandSurface p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white mb-4 shadow-sm">
                <i data-lucide="building-2" class="w-5 h-5"></i>
            </div>
            <h3 class="text-4xl font-extrabold text-primary mb-1">{{ $totalKecamatan }}</h3>
            <p class="text-xs text-gray-500 font-medium">Total Kecamatan</p>
        </div>

        <!-- Total Banjir -->
        <div class="bg-brandSurface p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white mb-4 shadow-sm">
                <i data-lucide="droplet" class="w-5 h-5"></i>
            </div>
            <h3 class="text-4xl font-extrabold text-primary mb-1">{{ $totalBanjir }}</h3>
            <p class="text-xs text-gray-500 font-medium">Total Banjir</p>
        </div>

        <!-- Total Kekeringan -->
        <div class="bg-brandSurface p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center">
            <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center text-white mb-4 shadow-sm">
                <i data-lucide="thermometer-sun" class="w-5 h-5"></i>
            </div>
            <h3 class="text-4xl font-extrabold text-primary mb-1">{{ $totalKekeringan }}</h3>
            <p class="text-xs text-gray-500 font-medium">Total Kekeringan</p>
        </div>

        <!-- Total Cuaca Ekstrem -->
        <div class="bg-brandSurface p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center">
            <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white mb-4 shadow-sm">
                <i data-lucide="wind" class="w-5 h-5"></i>
            </div>
            <h3 class="text-4xl font-extrabold text-primary mb-1">{{ $totalCuacaEkstrem }}</h3>
            <p class="text-xs text-gray-500 font-medium">Total Cuaca Ekstrem</p>
        </div>
    </div>

    <!-- Map Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Slider -->
        <div class="lg:col-span-3">
            <div class="bg-brandSurface p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-primary mb-4">Tahun Data: <span id="yearDisplay">Semua Tahun</span></h3>
                <input type="range" min="2020" max="{{ date('Y') }}" value="2020" class="w-full" id="yearSlider">
                <p class="text-xs text-gray-400 mt-3 text-center">*Tarik ke paling kiri untuk menampilkan Semua Tahun.</p>
            </div>
        </div>

        <!-- Center: Mini Map -->
        <div class="lg:col-span-6 h-[500px]">
            <div class="bg-brandSurface rounded-[2rem] shadow-sm border border-gray-100 h-full p-2 overflow-hidden">
                <div id="map"></div>
            </div>
        </div>

        <!-- Right: Active Kecamatan Details -->
        <div class="lg:col-span-3">
            <div class="bg-brandSurface p-6 rounded-3xl shadow-sm border border-gray-100 transition-all" id="detailCard">
                <p class="text-[10px] font-bold text-gray-500 tracking-widest uppercase mb-1">Kecamatan</p>
                <h3 class="text-2xl font-bold text-primary mb-3" id="detailKecamatan">-</h3>

                <div id="detailBadge" class="mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Pilih Area
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm font-medium">
                        <span class="flex items-center gap-2 text-gray-600"><i data-lucide="droplet" class="w-4 h-4 text-blue-500"></i> Banjir</span>
                        <span class="text-primary font-bold" id="detailBanjir">-</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium">
                        <span class="flex items-center gap-2 text-gray-600"><i data-lucide="thermometer-sun" class="w-4 h-4 text-green-500"></i> Kekeringan</span>
                        <span class="text-primary font-bold" id="detailKekeringan">-</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-medium">
                        <span class="flex items-center gap-2 text-gray-600"><i data-lucide="wind" class="w-4 h-4 text-gray-400"></i> Cuaca Ekstrem</span>
                        <span class="text-primary font-bold" id="detailCuaca">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer Overlay -->
    <div id="drawer-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300" onclick="closeDrawer()"></div>

    <!-- Drawer Panel -->
    <div id="report-drawer" class="fixed right-0 top-0 h-full w-full sm:w-96 bg-brandSurface shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-brandSurface mt-16 sm:mt-0">
            <div>
                <h2 class="text-lg font-bold text-gray-900" id="drawer-title">Detail Kecamatan</h2>
            </div>
            <button onclick="closeDrawer()" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto flex-1 bg-brandBg" id="drawer-content">
            <!-- Laporan cards will be injected here via JS -->
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Initialize map
    var map = L.map('map', { scrollWheelZoom: false, zoomControl: true }).setView([-6.238270, 106.975571], 10);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    var kecamatansData = @json($kecamatans);
    var laporans = @json($laporans);

    var kecStats = {};
    var currentSelectedKecamatan = null;

    function calculateStats(year) {
        kecamatansData.forEach(function(k) {
            kecStats[k.name.toUpperCase()] = {
                total: 0,
                banjir: 0,
                kekeringan: 0,
                cuaca: 0
            };
        });

        var highestKecName = '';
        var highestTotal = -1;

        laporans.forEach(function(l) {
            if (year != 0 && year != '0') {
                if (l.date && !l.date.startsWith(year.toString())) {
                    return; // Lewati jika tahun tidak cocok
                }
            }

            if (l.desa && l.desa.kecamatan && l.desa.kecamatan.name) {
                var kecName = l.desa.kecamatan.name.toUpperCase();
                kecStats[kecName].total++;

                if(l.jenis_bencana) {
                    if(l.jenis_bencana.name === 'Banjir') kecStats[kecName].banjir++;
                    else if(l.jenis_bencana.name === 'Kekeringan') kecStats[kecName].kekeringan++;
                    else if(l.jenis_bencana.name === 'Cuaca Ekstrem' || l.jenis_bencana.name === 'Angin Puting Beliung') kecStats[kecName].cuaca++;
                }
            }
        });

        // Cari kecamatan dengan risiko tertinggi
        for (var kec in kecStats) {
            if (kecStats[kec].total > highestTotal) {
                highestTotal = kecStats[kec].total;
                highestKecName = kec;
            }
        }

        return highestKecName;
    }

    var geojsonLayer;

    function getFeatureStyle(feature) {
        var kecName = feature.properties.NAME_3 ? feature.properties.NAME_3.toUpperCase() : '';
        var count = (kecStats[kecName] && kecStats[kecName].total) ? kecStats[kecName].total : 0;

        var fillColor = '#9ca3af'; // Aman
        if (count >= 1 && count <= 3) fillColor = '#10b981'; // Rendah
        if (count >= 4 && count <= 7) fillColor = '#eab308'; // Sedang
        if (count >= 8) fillColor = '#ef4444'; // Tinggi

        var weight = 2;
        var color = 'white';
        var fillOpacity = 0.9;

        // Pertahankan highlight jika sedang dipilih
        if (currentSelectedKecamatan && currentSelectedKecamatan.toUpperCase() === kecName) {
            weight = 3;
            color = '#12395C';
            fillOpacity = 1;
        }

        return { color: color, weight: weight, fillColor: fillColor, fillOpacity: fillOpacity };
    }

    fetch('/geo/kecamatan.geojson')
        .then(response => response.json())
        .then(data => {
            // Hitung awal (Semua Tahun)
            var highest = calculateStats(0);

            geojsonLayer = L.geoJSON(data, {
                style: getFeatureStyle,
                onEachFeature: function (feature, layer) {
                    if (feature.properties && feature.properties.NAME_3) {
                        var kecName = feature.properties.NAME_3;

                        layer.on('click', function(e) {
                            currentSelectedKecamatan = kecName;
                            geojsonLayer.setStyle(getFeatureStyle);
                            updateDetailCard(kecName);
                            openDrawer(kecName);
                        });
                    }
                }
            }).addTo(map);
            map.fitBounds(geojsonLayer.getBounds());

            // Tampilkan kecamatan risiko tertinggi secara default
            if (highest && !currentSelectedKecamatan) {
                updateDetailCard(highest);
            }
        });

    function updateDetailCard(kecName) {
        // Ubah title menjadi sentence case agar lebih rapi
        var displayKecName = kecName.charAt(0).toUpperCase() + kecName.slice(1).toLowerCase();

        var nameUpper = kecName.toUpperCase();
        var stats = kecStats[nameUpper] || { total: 0, banjir: 0, kekeringan: 0, cuaca: 0 };

        document.getElementById('detailKecamatan').innerText = displayKecName;
        document.getElementById('detailBanjir').innerText = stats.banjir;
        document.getElementById('detailKekeringan').innerText = stats.kekeringan;
        document.getElementById('detailCuaca').innerText = stats.cuaca;

        var badgeHtml = '';
        if (stats.total >= 8) {
            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Tinggi</span>`;
        } else if (stats.total >= 4) {
            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Sedang</span>`;
        } else if (stats.total >= 1) {
            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Rendah</span>`;
        } else {
            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Aman</span>`;
        }
        document.getElementById('detailBadge').innerHTML = badgeHtml;
    }

    // Slider Logic
    var slider = document.getElementById('yearSlider');
    var display = document.getElementById('yearDisplay');

    slider.addEventListener('input', function(e) {
        var val = e.target.value;
        if (val <= 2019) {
            display.innerText = "Semua Tahun";
            var highest = calculateStats(0); // 0 untuk semua tahun
        } else {
            display.innerText = val;
            var highest = calculateStats(val);
        }

        if (geojsonLayer) {
            geojsonLayer.setStyle(getFeatureStyle);
        }

        if (currentSelectedKecamatan) {
            updateDetailCard(currentSelectedKecamatan);
        } else if (highest) {
            updateDetailCard(highest);
        }
    });

    // Drawer Logic
    function openDrawer(kecamatanName) {
        document.getElementById('drawer-overlay').classList.remove('hidden');
        document.getElementById('report-drawer').classList.remove('translate-x-full');
        document.getElementById('drawer-title').innerText = 'Kec. ' + kecamatanName;

        var currentYear = document.getElementById('yearSlider').value;

        var filteredLaporans = laporans.filter(function(l) {
            var matchKecamatan = l.desa
            var matchYear = true;
            if (currentYear > 2019) {
                if (l.date && !l.date.startsWith(currentYear.toString())) {
                    matchYear = false;
                }
            }
            return matchKecamatan && matchYear;
        });

        // document.getElementById('drawer-subtitle').innerText = filteredLaporans.length + ' Laporan Bencana';

        var contentHtml = '';
        if(filteredLaporans.length === 0) {
            contentHtml = `<div class="text-center py-10 text-gray-500 flex flex-col items-center">
                <i data-lucide="shield-check" class="w-12 h-12 mb-3 text-green-500"></i>
                <p class="text-sm">Tidak ada laporan bencana pada periode ini.</p>
            </div>`;
        } else {
            filteredLaporans.forEach(function(l) {
                var statusBadge = l.status === 'active'
                    ? `<span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[10px] uppercase font-bold tracking-wider border border-red-200">Aktif</span>`
                    : `<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] uppercase font-bold tracking-wider border border-green-200">Selesai</span>`;

                contentHtml += `
                    <div class="bg-brandSurface rounded-lg shadow-sm border border-gray-200 p-4 mb-3 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <h4 class="font-bold text-gray-900 text-sm leading-tight flex-1">${l.jenis_bencana.name}</h4>
                            <div class="flex-shrink-0 mt-0.5">${statusBadge}</div>
                        </div>
                        <div class="space-y-1.5 mb-3">
                            <p class="text-xs text-gray-600 flex items-center gap-1.5"><i data-lucide="tag" class="w-3.5 h-3.5 text-gray-400"></i> ${(l.jenis_bencana ? l.jenis_bencana.name : '-')}</p>
                            <p class="text-xs text-gray-600 flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i> Desa ${(l.desa.name ? l.desa.name : '-')}</p>
                            <p class="text-xs text-gray-600 flex items-center gap-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i> ${new Date(l.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
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
</script>
@endpush
