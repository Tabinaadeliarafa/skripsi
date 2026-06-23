@extends('layouts.app')

@section('content')
<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="mb-6 bg-brandSurface p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-2">
        <i data-lucide="table" class="w-5 h-5 text-gray-500"></i>
        <h2 class="text-lg font-semibold text-gray-900">Rekapitulasi Laporan Bencana</h2>
    </div>
    
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <form method="GET" action="/laporan" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="jenis_bencana_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary max-w-[150px] truncate" onchange="this.form.submit()">
                <option value="">Semua Bencana</option>
                @foreach($jenisBencanas as $jenis)
                    <option value="{{ $jenis->id }}" {{ request('jenis_bencana_id') == $jenis->id ? 'selected' : '' }}>
                        {{ $jenis->name }}
                    </option>
                @endforeach
            </select>

            <select name="kecamatan_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary max-w-[150px] truncate" onchange="this.form.submit()">
                <option value="">Semua Kecamatan</option>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ request('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->name }}
                    </option>
                @endforeach
            </select>
        </form>

        <button type="button" id="btn-export" onclick="exportToPDF()" class="text-sm bg-red-600 text-white px-4 py-2 rounded-md font-medium hover:opacity-90 transition-colors flex items-center gap-2 shadow-sm whitespace-nowrap">
            <i data-lucide="file-text" class="w-4 h-4"></i> Export PDF
        </button>
    </div>
</div>

<!-- Wrapper for PDF Export -->
<div id="export-area" class="bg-slate-50 p-4 -m-4 sm:p-0 sm:m-0 sm:bg-transparent flex flex-col gap-6">

        <!-- Table -->
    <div class="bg-brandSurface rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Bencana</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah (Desa/Kecamatan)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporans as $laporan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ date('d M Y', strtotime($laporan->date)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $laporan->jenisBencana ? $laporan->jenisBencana->name : '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($laporan->desa)
                                Desa {{ $laporan->desa->name }}<br>
                                <span class="text-xs text-gray-500">Kec. {{ $laporan->desa->kecamatan ? $laporan->desa->kecamatan->name : '-' }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $laporan->title }}">
                            <strong class="text-gray-900 block truncate">{{ $laporan->title }}</strong>
                            <span class="truncate block">{{ $laporan->description ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($laporan->status == 'active')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs uppercase font-bold tracking-wider border border-red-200">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs uppercase font-bold tracking-wider border border-green-200">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                            Tidak ada data laporan bencana yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($laporans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50" id="pagination-links">
            {{ $laporans->links() }}
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function exportToPDF() {
        var element = document.getElementById('export-area');
        var btn = document.getElementById('btn-export');
        var pagination = document.getElementById('pagination-links');
        
        var originalBtnText = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Mengekspor...';
        btn.disabled = true;

        if (pagination) pagination.style.display = 'none';

        var opt = {
            margin:       0.3,
            filename:     'Laporan_Bencana_Kab_Bekasi.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(function() {
            btn.innerHTML = originalBtnText;
            btn.disabled = false;
            if (pagination) pagination.style.display = 'block';
            lucide.createIcons();
        }).catch(function(err) {
            console.error("PDF Export error: ", err);
            btn.innerHTML = originalBtnText;
            btn.disabled = false;
            if (pagination) pagination.style.display = 'block';
        });
    }
</script>
@endpush
