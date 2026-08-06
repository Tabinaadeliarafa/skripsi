@extends('layouts.app')

@section('content')
<div class="mb-6 bg-brandSurface p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-2">
        <i data-lucide="bar-chart-2" class="w-5 h-5 text-gray-500"></i>
        <h2 class="text-lg font-semibold text-gray-900">Visualisasi Data Bencana</h2>
    </div>
    <form method="GET" action="/visualisasi" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <div class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary">
            <span class="text-gray-500 text-sm">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary">
        </div>
        <select name="kecamatan_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary max-w-[150px] truncate">
            <option value="">Semua Kecamatan</option>
            @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}" {{ request('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                    {{ $kecamatan->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="text-sm bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-md font-medium hover:bg-gray-200 transition-colors">
            Filter
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Pie Chart -->
    <div class="bg-brandSurface p-6 rounded-xl border border-gray-200 shadow-sm col-span-1">
        <h3 class="text-base font-bold text-gray-800 mb-4 text-center">Komposisi Jenis Bencana</h3>
        <div id="jenisBencanaChart" class="w-full flex justify-center"></div>
    </div>

    <!-- Bar Chart Wilayah -->
    <div class="bg-brandSurface p-6 rounded-xl border border-gray-200 shadow-sm col-span-1 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-gray-800">
                @if($isKecamatanSelected)
                    Perbandingan Antar Desa/Kelurahan
                @else
                    Perbandingan Antar Kecamatan
                @endif
            </h3>
            <span class="text-xs font-medium bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md">Total Kejadian</span>
        </div>
        <div id="wilayahChart" class="w-full"></div>
    </div>

    <!-- Bar Chart Tahunan -->
    <div class="bg-brandSurface p-6 rounded-xl border border-gray-200 shadow-sm col-span-1 lg:col-span-3">
        <h3 class="text-base font-bold text-gray-800 mb-4 text-center">Tren Bencana Tahunan</h3>
        <div id="trenTahunanChart" class="w-full"></div>
    </div>

    <!-- Line Chart Prediksi -->
    <div class="bg-brandSurface p-6 rounded-xl border border-gray-200 shadow-sm col-span-1 lg:col-span-3 mt-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-gray-800">Prediksi Tren Kejadian (LSTM)</h3>
            <span class="text-xs font-medium bg-secondary/10 text-secondary px-2.5 py-1 rounded-md">{{ !empty($chartPrediksi['labels']) ? 'Proyeksi Tahun ' . last($chartPrediksi['labels']) : 'LSTM' }}</span>
        </div>
        @if(($chartPrediksi['status'] ?? 'unavailable') !== 'ok')
            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ $chartPrediksi['message'] ?? 'Prediksi LSTM belum tersedia.' }}
            </div>
        @elseif(isset($chartPrediksi['rmse']))
            <div class="mb-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                <span>Metode: {{ $chartPrediksi['method'] }} · RMSE data latih: {{ $chartPrediksi['rmse'] }}</span>
                @if(!empty($chartPrediksi['risk']))
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 font-semibold"
                          style="color: {{ $chartPrediksi['risk']['color'] }}; border-color: {{ $chartPrediksi['risk']['color'] }}33; background-color: {{ $chartPrediksi['risk']['color'] }}14;">
                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $chartPrediksi['risk']['color'] }};"></span>
                        {{ $chartPrediksi['risk']['label'] }}
                    </span>
                @endif
            </div>
        @endif
        <div id="prediksiChart" class="w-full"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Data untuk Chart Jenis Bencana (Pie)
    const chartJenisData = @json($chartJenis);
    const jenisLabels = Object.keys(chartJenisData);
    const jenisValues = Object.values(chartJenisData);

    // Palet Warna yang lebih modern
    const colors = {
        'Banjir': '#3b82f6', // blue-500
        'Kekeringan': '#eab308', // yellow-500
        'Angin Puting Beliung': '#64748b', // slate-500
        'Cuaca Ekstrem': '#64748b', 
        'Default': '#94a3b8'
    };

    const pieColors = jenisLabels.map(label => colors[label] || colors['Default']);

    // ApexCharts: Pie/Donut Chart
    var optionsPie = {
        series: jenisValues,
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Inter, poppins',
        },
        labels: jenisLabels,
        colors: pieColors,
        dataLabels: {
            enabled: true,
            dropShadow: { enabled: false }
        },
        plotOptions: {
            pie: {
                donut: { size: '60%' }
            }
        },
        legend: {
            position: 'bottom'
        },
        stroke: { show: false }
    };

    var chartPie = new ApexCharts(document.querySelector("#jenisBencanaChart"), optionsPie);
    chartPie.render();

    // Data untuk Chart Tren Tahunan (Bar)
    const chartTahunData = @json($chartTahun);
    const tahunLabels = Object.keys(chartTahunData);
    
    // Siapkan dataset berdasarkan jenis bencana yang unik di semua tahun
    const uniqueJenis = [...new Set(jenisLabels)];
    
    const datasets = uniqueJenis.map(jenis => {
        return {
            name: jenis,
            data: tahunLabels.map(tahun => chartTahunData[tahun][jenis] || 0)
        };
    });
    
    const barColors = uniqueJenis.map(label => colors[label] || colors['Default']);

    // ApexCharts: Bar Chart
    var optionsBar = {
        series: datasets,
        chart: {
            type: 'bar',
            height: 300,
            fontFamily: 'Inter, poppins',
            toolbar: { show: false }
        },
        colors: barColors,
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%',
                borderRadius: 4
            },
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: {
            categories: tahunLabels,
        },
        yaxis: {
            title: { text: 'Jumlah Kejadian' }
        },
        fill: { opacity: 1 },
        legend: { position: 'bottom' },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Kejadian"
                }
            }
        }
    };

    var chartBar = new ApexCharts(document.querySelector("#trenTahunanChart"), optionsBar);
    chartBar.render();

    // Data untuk Chart Wilayah (Bar Horizontal / Vertikal)
    const chartWilayahData = @json($chartWilayah);
    const wilayahLabels = Object.keys(chartWilayahData);
    const wilayahValues = Object.values(chartWilayahData);

    var optionsWilayah = {
        series: [{
            name: 'Total Kejadian',
            data: wilayahValues
        }],
        chart: {
            type: 'bar',
            height: 300,
            fontFamily: 'Inter, poppins',
            toolbar: { show: false }
        },
        colors: ['#12395C'], // Menggunakan primary color brand
        plotOptions: {
            bar: {
                horizontal: true, // Lebih baik horizontal jika namanya panjang (Kecamatan/Desa)
                borderRadius: 4,
                barHeight: '60%',
            }
        },
        dataLabels: { 
            enabled: true,
            formatter: function(val) { return val; },
            style: { colors: ['#fff'] }
        },
        stroke: { show: false },
        xaxis: {
            categories: wilayahLabels,
            labels: { show: false } // Sembunyikan label X karena sudah ada di batang
        },
        yaxis: {
            labels: {
                style: {
                    fontWeight: 600,
                }
            }
        },
        grid: {
            xaxis: { lines: { show: false } },   
            yaxis: { lines: { show: false } },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Kejadian"
                }
            }
        }
    };

    var chartWilayah = new ApexCharts(document.querySelector("#wilayahChart"), optionsWilayah);
    chartWilayah.render();

    // Data untuk Chart Prediksi LSTM (Line)
    const chartPrediksi = @json($chartPrediksi);
    
    var optionsPrediksi = {
        series: [
            {
                name: 'Data Historis',
                data: chartPrediksi.historis
            },
            {
                name: 'LSTM (Prediksi)',
                data: chartPrediksi.lstm
            }
        ],
        chart: {
            type: 'line',
            height: 350,
            fontFamily: 'Inter, poppins',
            toolbar: { show: false }
        },
        colors: ['#D45B1F', '#3b82f6'],
        stroke: {
            width: [3, 3],
            curve: 'smooth',
            dashArray: [0, 5]
        },
        markers: {
            size: 5,
            discrete: chartPrediksi.risk ? [
                {
                    seriesIndex: 1,
                    dataPointIndex: chartPrediksi.labels.length - 1,
                    fillColor: chartPrediksi.risk.color,
                    strokeColor: '#ffffff',
                    size: 9,
                    shape: 'circle'
                }
            ] : []
        },
        xaxis: {
            categories: chartPrediksi.labels,
        },
        yaxis: {
            title: { text: 'Jumlah Kejadian' }
        },
        legend: { position: 'top' },
        tooltip: {
            y: {
                formatter: function (val, opts) {
                    if (val === null) return "Tidak ada data historis";

                    const isForecastPoint = opts.seriesIndex === 1
                        && opts.dataPointIndex === chartPrediksi.labels.length - 1;

                    if (isForecastPoint && chartPrediksi.risk) {
                        return val + " Kejadian · " + chartPrediksi.risk.label;
                    }

                    return val + " Kejadian";
                }
            }
        }
    };

    var chartPrediksiChart = new ApexCharts(document.querySelector("#prediksiChart"), optionsPrediksi);
    chartPrediksiChart.render();
</script>
@endpush
