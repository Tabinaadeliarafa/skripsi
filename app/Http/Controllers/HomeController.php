<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use App\Models\JenisBencana;
use App\Services\LstmPredictionService;

class HomeController extends Controller
{

    /**
     * Menentukan kategori risiko prediksi berdasarkan distribusi total kejadian
     * tahunan historis. Batas rendah dan tinggi memakai persentil 33% dan 67%.
     */
    private function calculatePredictionRisk(array $historicalTotals, int $forecastTotal): array
    {
        $values = array_values(array_filter($historicalTotals, fn ($value) => is_numeric($value)));
        sort($values, SORT_NUMERIC);

        if (count($values) < 3) {
            return [
                'label' => 'Belum dapat diklasifikasikan',
                'level' => 'unknown',
                'color' => '#64748b',
                'low_threshold' => null,
                'high_threshold' => null,
            ];
        }

        $percentile = function (array $sortedValues, float $p): float {
            $position = (count($sortedValues) - 1) * $p;
            $lower = (int) floor($position);
            $upper = (int) ceil($position);

            if ($lower === $upper) {
                return (float) $sortedValues[$lower];
            }

            $weight = $position - $lower;
            return ((float) $sortedValues[$lower] * (1 - $weight))
                + ((float) $sortedValues[$upper] * $weight);
        };

        $lowThreshold = $percentile($values, 0.33);
        $highThreshold = $percentile($values, 0.67);

        if ($forecastTotal <= $lowThreshold) {
            $label = 'Risiko Rendah';
            $level = 'low';
            $color = '#10b981';
        } elseif ($forecastTotal <= $highThreshold) {
            $label = 'Risiko Sedang';
            $level = 'medium';
            $color = '#eab308';
        } else {
            $label = 'Risiko Tinggi';
            $level = 'high';
            $color = '#ef4444';
        }

        return [
            'label' => $label,
            'level' => $level,
            'color' => $color,
            'low_threshold' => round($lowThreshold, 2),
            'high_threshold' => round($highThreshold, 2),
        ];
    }

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

        // PREDIKSI LSTM
        $monthlySeries = [];
        foreach ($data as $item) {
            $period = date('Ym', strtotime($item->date));
            $monthlySeries[$period] = ($monthlySeries[$period] ?? 0) + 1;
        }

        $lstmResult = app(LstmPredictionService::class)->forecast($monthlySeries);

        $totalPerYear = [];
        foreach ($data as $item) {
            $year = (int) date('Y', strtotime($item->date));
            $totalPerYear[$year] = ($totalPerYear[$year] ?? 0) + 1;
        }
        ksort($totalPerYear);

        if (
            $lstmResult['status'] === 'ok'
            && isset($lstmResult['last_historical_year'])
        ) {
            $lastHistoricalYear = (int) $lstmResult['last_historical_year'];
            $totalPerYear = array_filter(
                $totalPerYear,
                fn ($value, $year) => (int) $year <= $lastHistoricalYear,
                ARRAY_FILTER_USE_BOTH
            );
        }

        $chartPrediksi = [
            'labels' => array_map('strval', array_keys($totalPerYear)),
            'historis' => array_values($totalPerYear),
            'lstm' => array_fill(0, count($totalPerYear), null),
            'status' => $lstmResult['status'],
            'message' => $lstmResult['message'] ?? null,
            'method' => $lstmResult['method'] ?? 'LSTM',
            'rmse' => $lstmResult['rmse'] ?? null,
            'risk' => null,
            'forecast_year' => null,
            'monthly_labels' => [],
            'monthly_forecast' => [],
        ];

        if ($lstmResult['status'] === 'ok') {
            $forecastTotal = (int) $lstmResult['forecast_total'];
            $chartPrediksi['forecast_year'] = (int) $lstmResult['forecast_year'];
            $chartPrediksi['monthly_labels'] = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            ];
            $chartPrediksi['monthly_forecast'] = array_map(
                fn ($value) => round(max(0, (float) $value), 2),
                array_slice($lstmResult['monthly_forecast'] ?? [], 0, 12)
            );
            $chartPrediksi['risk'] = $this->calculatePredictionRisk(
                array_values($totalPerYear),
                $forecastTotal
            );
            $chartPrediksi['labels'][] = (string) $lstmResult['forecast_year'];
            $chartPrediksi['historis'][] = null;
            $chartPrediksi['lstm'][] = $forecastTotal;

            // Sambungkan garis prediksi dari nilai aktual terakhir agar grafik mudah dibaca.
            if (count($chartPrediksi['lstm']) >= 2 && $totalPerYear !== []) {
                $chartPrediksi['lstm'][count($chartPrediksi['lstm']) - 2] = end($totalPerYear);
            }
        }

        // PETA RISIKO PREDIKSI PER KECAMATAN
        $mapQuery = LaporanBencana::with('desa.kecamatan');
        if ($request->filled('start_date')) {
            $mapQuery->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $mapQuery->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('jenis_bencana_id')) {
            $mapQuery->where('jenis_bencana_id', $request->jenis_bencana_id);
        }

        $mapRows = $mapQuery->get()->groupBy(
            fn ($item) => $item->desa?->kecamatan?->id
        );
        $predictionMap = [];
        $forecastTotals = [];

        $historicalCounts = [];
        foreach ($kecamatans as $kecamatan) {
            $historicalCounts[$kecamatan->id] = $mapRows
                ->get($kecamatan->id, collect())
                ->count();
        }

        $allocatedForecasts = [];
        $totalHistoricalCount = array_sum($historicalCounts);

        if ($lstmResult['status'] === 'ok' && $totalHistoricalCount > 0) {
            $overallForecastTotal = max(0, (int) $lstmResult['forecast_total']);
            $remainders = [];
            $allocatedTotal = 0;

            foreach ($historicalCounts as $kecamatanId => $historicalCount) {
                $rawForecast = $overallForecastTotal
                    * ($historicalCount / $totalHistoricalCount);
                $allocatedForecasts[$kecamatanId] = (int) floor($rawForecast);
                $remainders[$kecamatanId] = $rawForecast - floor($rawForecast);
                $allocatedTotal += $allocatedForecasts[$kecamatanId];
            }
            
            arsort($remainders, SORT_NUMERIC);
            $remaining = $overallForecastTotal - $allocatedTotal;
            foreach (array_keys($remainders) as $kecamatanId) {
                if ($remaining <= 0) {
                    break;
                }

                $allocatedForecasts[$kecamatanId]++;
                $remaining--;
            }
        }

        foreach ($kecamatans as $kecamatan) {
            $forecastTotal = $allocatedForecasts[$kecamatan->id] ?? null;
            $hasPrediction = $forecastTotal !== null;

            $predictionMap[$kecamatan->name] = [
                'name' => $kecamatan->name,
                'status' => $hasPrediction ? 'ok' : 'unavailable',
                'message' => $hasPrediction
                    ? null
                    : ($lstmResult['message'] ?? 'Data historis belum cukup untuk prediksi.'),
                'forecast_year' => $hasPrediction
                    ? ($lstmResult['forecast_year'] ?? null)
                    : null,
                'forecast_total' => $forecastTotal,
                'rmse' => $hasPrediction ? ($lstmResult['rmse'] ?? null) : null,
                'risk' => null,
            ];

            if ($hasPrediction) {
                $forecastTotals[] = $forecastTotal;
            }
        }

        // Kategori warna bersifat relatif terhadap distribusi hasil prediksi seluruh
        // kecamatan: 33% terbawah rendah, 34% tengah sedang, dan 33% teratas tinggi.
        foreach ($predictionMap as &$prediction) {
            if ($prediction['forecast_total'] !== null) {
                $prediction['risk'] = $this->calculatePredictionRisk(
                    $forecastTotals,
                    $prediction['forecast_total']
                );
            }
        }
        unset($prediction);

        return view('pages.visualisasi', compact('kecamatans', 'jenisBencanas', 'request', 'chartJenis', 'chartTahun', 'chartWilayah', 'isKecamatanSelected', 'chartPrediksi', 'predictionMap'));
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
