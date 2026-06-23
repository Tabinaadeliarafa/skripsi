<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use App\Models\JenisBencana;

class HomeController extends Controller
{
    private function applyFilters($query, Request $request)
    {
        if ($request->has('kecamatan_id') && $request->kecamatan_id != '') {
            $query->whereHas('desa', function($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            });
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->has('jenis_bencana_id') && $request->jenis_bencana_id != '') {
            $query->where('jenis_bencana_id', $request->jenis_bencana_id);
        }
        
        return $query;
    }

    public function index(Request $request)
    {
        // Statistik spesifik
        $totalKecamatan = Kecamatan::count();
        $totalBanjir = LaporanBencana::whereHas('jenisBencana', function($q) { $q->where('name', 'Banjir'); })->count();
        $totalKekeringan = LaporanBencana::whereHas('jenisBencana', function($q) { $q->where('name', 'Kekeringan'); })->count();
        $totalCuacaEkstrem = LaporanBencana::whereHas('jenisBencana', function($q) { $q->whereIn('name', ['Cuaca Ekstrem', 'Angin Puting Beliung']); })->count();
        
        // Informasi Risiko Tertinggi
        $kecamatanRisikoTinggi = Kecamatan::withCount('laporanBencanas')
            ->orderBy('laporan_bencanas_count', 'desc')
            ->first();

        $breakdownKejadian = collect();
        if ($kecamatanRisikoTinggi) {
            $breakdownKejadian = LaporanBencana::whereHas('desa', function($q) use ($kecamatanRisikoTinggi) {
                $q->where('kecamatan_id', $kecamatanRisikoTinggi->id);
            })->with('jenisBencana')
              ->get()
              ->groupBy(function($item) {
                  return $item->jenisBencana ? $item->jenisBencana->name : 'Lainnya';
              })->map->count();
        }
        
        // Data untuk Mini Map
        $kecamatans = Kecamatan::select('id', 'name', 'indeks_bahaya')->withCount('desas')->get();
        $laporans = LaporanBencana::with([
            'desa' => function($q) { $q->select('id', 'name', 'kecamatan_id'); },
            'desa.kecamatan' => function($q) { $q->select('id', 'name'); }, 
            'jenisBencana' => function($q) { $q->select('id', 'name'); }
        ])->select('id', 'desa_id', 'jenis_bencana_id', 'title', 'date', 'status')->get()->map(function($laporan) {
            return [
                'id' => $laporan->id,
                'date' => $laporan->date,
                'jenis_bencana' => $laporan->jenisBencana ? ['name' => $laporan->jenisBencana->name] : null,
                'desa' => $laporan->desa ? [
                    'name' => $laporan->desa->name,
                    'kecamatan' => $laporan->desa->kecamatan ? ['name' => $laporan->desa->kecamatan->name] : null
                ] : null,
            ];
        });

        return view('pages.beranda', compact(
            'totalKecamatan', 'totalBanjir', 'totalKekeringan', 'totalCuacaEkstrem', 
            'kecamatanRisikoTinggi', 'breakdownKejadian',
            'kecamatans', 'laporans'
        ));
    }

    public function peta(Request $request)
    {
        $kecamatans = Kecamatan::select('id', 'name', 'indeks_bahaya')->withCount('desas')->get();
        $jenisBencanas = JenisBencana::all();
        
        $query = LaporanBencana::with([
            'desa' => function($q) { $q->select('id', 'name', 'kecamatan_id'); },
            'desa.kecamatan' => function($q) { $q->select('id', 'name'); }, 
            'jenisBencana' => function($q) { $q->select('id', 'name'); }
        ])->select('id', 'desa_id', 'jenis_bencana_id', 'title', 'description', 'latitude', 'longitude', 'date', 'status');
        
        // Removed applyFilters so JS can dynamically filter all data
        // $query = $this->applyFilters($query, $request);
        
        $laporans = $query->get()->map(function($laporan) {
            return [
                'id' => $laporan->id,
                'title' => $laporan->title,
                'description' => $laporan->description,
                'latitude' => $laporan->latitude,
                'longitude' => $laporan->longitude,
                'date' => $laporan->date,
                'status' => $laporan->status,
                'jenis_bencana' => $laporan->jenisBencana ? ['name' => $laporan->jenisBencana->name] : null,
                'desa' => $laporan->desa ? [
                    'name' => $laporan->desa->name,
                    'kecamatan' => $laporan->desa->kecamatan ? ['name' => $laporan->desa->kecamatan->name] : null
                ] : null,
            ];
        });
        
        return view('pages.peta', compact('kecamatans', 'laporans', 'jenisBencanas', 'request'));
    }

    public function visualisasi(Request $request)
    {
        $kecamatans = Kecamatan::select('id', 'name')->get();
        $jenisBencanas = JenisBencana::all();
        
        $query = LaporanBencana::with(['jenisBencana', 'desa.kecamatan']);
        $query = $this->applyFilters($query, $request);
        $data = $query->get();
        
        // Data Grafik Jenis Bencana
        $chartJenis = $data->groupBy(function($item) {
            return $item->jenisBencana ? $item->jenisBencana->name : 'Lainnya';
        })->map->count();
        
        // Data Grafik Tahunan (Perbandingan Banjir, Kekeringan, Cuaca Ekstrem)
        $chartTahun = [];
        foreach($data as $item) {
            $year = date('Y', strtotime($item->date));
            $jenis = $item->jenisBencana ? $item->jenisBencana->name : 'Lainnya';
            if(!isset($chartTahun[$year])) {
                $chartTahun[$year] = [];
            }
            if(!isset($chartTahun[$year][$jenis])) {
                $chartTahun[$year][$jenis] = 0;
            }
            $chartTahun[$year][$jenis]++;
        }
        ksort($chartTahun);

        // Data Grafik Wilayah (Kecamatan atau Desa)
        $chartWilayah = [];
        $isKecamatanSelected = $request->filled('kecamatan_id');
        
        foreach($data as $item) {
            if($isKecamatanSelected) {
                $wilayahName = $item->desa ? $item->desa->name : 'Lainnya';
            } else {
                $wilayahName = ($item->desa && $item->desa->kecamatan) ? $item->desa->kecamatan->name : 'Lainnya';
            }
            
            if(!isset($chartWilayah[$wilayahName])) {
                $chartWilayah[$wilayahName] = 0;
            }
            $chartWilayah[$wilayahName]++;
        }
        arsort($chartWilayah);

        // --- PREDIKSI MOVING AVERAGE ---
        $chartPrediksi = [
            'labels' => [],
            'historis' => [],
            'moving_average' => []
        ];

        $totalPerYear = [];
        foreach($data as $item) {
            $year = date('Y', strtotime($item->date));
            if(!isset($totalPerYear[$year])) {
                $totalPerYear[$year] = 0;
            }
            $totalPerYear[$year]++;
        }
        
        if (count($totalPerYear) > 0) {
            $actualMaxYear = max(array_keys($totalPerYear));
            $minYear = min(array_keys($totalPerYear));
            $targetYear = 2026;
            
            for ($y = $minYear; $y <= $actualMaxYear; $y++) {
                if (!isset($totalPerYear[$y])) {
                    $totalPerYear[$y] = 0; 
                }
            }
            ksort($totalPerYear);

            $period = 3; 
            $series = [];

            $maxIterYear = max($actualMaxYear, $targetYear);
            
            for ($y = $minYear; $y <= $maxIterYear; $y++) {
                $chartPrediksi['labels'][] = $y;
                
                if ($y <= $actualMaxYear) {
                    $val = $totalPerYear[$y];
                    $chartPrediksi['historis'][] = $val;
                    $series[] = $val;
                } else {
                    $chartPrediksi['historis'][] = null;
                    $lastPeriod = array_slice($series, -$period);
                    $forecast = count($lastPeriod) > 0 ? round(array_sum($lastPeriod) / count($lastPeriod), 2) : 0;
                    $series[] = $forecast;
                }
                
                $lastPeriodForMA = array_slice($series, -$period);
                $ma = count($lastPeriodForMA) > 0 ? round(array_sum($lastPeriodForMA) / count($lastPeriodForMA), 2) : 0;
                $chartPrediksi['moving_average'][] = $ma;
            }
        }

        return view('pages.visualisasi', compact('kecamatans', 'jenisBencanas', 'request', 'chartJenis', 'chartTahun', 'chartWilayah', 'isKecamatanSelected', 'chartPrediksi'));
    }

    public function laporan(Request $request)
    {
        $kecamatans = Kecamatan::select('id', 'name')->get();
        $jenisBencanas = JenisBencana::all();
        
        $query = LaporanBencana::with(['jenisBencana', 'desa.kecamatan'])->orderBy('date', 'desc');
        $query = $this->applyFilters($query, $request);
        
        // Get all filtered data to calculate map stats
        $allData = clone $query;
        $allData = $allData->get();
        
        $kecStatsLaporan = [];
        foreach($allData as $item) {
            if($item->desa && $item->desa->kecamatan) {
                $kecName = strtoupper($item->desa->kecamatan->name);
                if(!isset($kecStatsLaporan[$kecName])) {
                    $kecStatsLaporan[$kecName] = 0;
                }
                $kecStatsLaporan[$kecName]++;
            }
        }
        
        // Using pagination for the table
        $laporans = $query->paginate(20)->withQueryString();
        
        return view('pages.laporan', compact('kecamatans', 'jenisBencanas', 'request', 'laporans', 'kecStatsLaporan'));
    }
}
