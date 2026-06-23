<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        $geoJsonString = file_get_contents(public_path('geo/kecamatan.geojson'));
        $geoData = json_decode($geoJsonString, true);

        $kecamatans = [];
        $kecamatanMap = []; // For mapping NAME_3 to ID

        if (isset($geoData['features'])) {
            foreach ($geoData['features'] as $feature) {
                // Name of Kecamatan is usually in NAME_3 or NAME
                $name = $feature['properties']['NAME_3'] ?? $feature['properties']['NAME'] ?? 'Unknown';

                // Random indeks_bahaya between 0.1 and 0.9 for dummy data
                $indeks = mt_rand(10, 90) / 100;

                // Color based on risk level
                $color = '#2563eb'; // Default blue
                if ($indeks <= 0.333) $color = '#10b981'; // Green
                elseif ($indeks <= 0.666) $color = '#f59e0b'; // Amber
                else $color = '#ef4444'; // Red

                $kecamatan = \App\Models\Kecamatan::create([
                    'name' => $name,
                    'color_code' => $color,
                    'indeks_bahaya' => $indeks,
                    'geojson' => json_encode($feature), // Save the specific feature GeoJSON
                ]);

                $kecamatans[] = $kecamatan;
                $kecamatanMap[strtoupper($name)] = $kecamatan->id;
            }
        }

        // Seed Desas from desa.geojson
        $desaGeoJsonString = file_get_contents(public_path('geo/desa.geojson'));
        $desaGeoData = json_decode($desaGeoJsonString, true);

        if (isset($desaGeoData['features'])) {
            foreach ($desaGeoData['features'] as $feature) {
                $kecamatanName = strtoupper($feature['properties']['NAME_3'] ?? '');
                $desaName = $feature['properties']['NAME_4'] ?? 'Unknown';

                if (isset($kecamatanMap[$kecamatanName])) {
                    \App\Models\Desa::create([
                        'kecamatan_id' => $kecamatanMap[$kecamatanName],
                        'name' => $desaName,
                        'geojson' => json_encode($feature),
                    ]);
                }
            }
        }

        // Read Laporan Bencana from CSV
        $csvFile = public_path('data/laporan.csv');
        if (file_exists($csvFile)) {
            $csvData = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $isHeader = true;

            $jenisMap = [];

            foreach ($csvData as $line) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $cols = explode(';', $line);
                if (count($cols) >= 4) {
                    $jenisNameLower = strtolower(trim($cols[0]));
                    $tahun = trim($cols[1]);
                    $kecamatanName = strtoupper(trim($cols[2]));
                    $desaName = strtoupper(trim($cols[3]));

                    // Normalize to 3 specific types
                    if (str_contains($jenisNameLower, 'banjir') || str_contains($jenisNameLower, 'rob')) {
                        $jenisName = 'Banjir';
                    } elseif (str_contains($jenisNameLower, 'kering')) {
                        $jenisName = 'Kekeringan';
                    } elseif (str_contains($jenisNameLower, 'angin') || str_contains($jenisNameLower, 'puting') || str_contains($jenisNameLower, 'cuaca') || str_contains($jenisNameLower, 'pohon') || str_contains($jenisNameLower, 'pasang') || str_contains($jenisNameLower, 'ekstrim') || str_contains($jenisNameLower, 'exstrim')) {
                        $jenisName = 'Cuaca Ekstrem';
                    } else {
                        continue; // Skip other disasters
                    }

                    // Find or create Jenis Bencana
                    if (!isset($jenisMap[$jenisName])) {
                        $jenis = \App\Models\JenisBencana::firstOrCreate(['name' => $jenisName]);
                        $jenisMap[$jenisName] = $jenis->id;
                    }
                    $jenisId = $jenisMap[$jenisName];

                    // Find Kecamatan
                    if (isset($kecamatanMap[$kecamatanName])) {
                        $kecId = $kecamatanMap[$kecamatanName];

                        // Find Desa
                        $desaId = null;
                        if ($desaName != '') {
                            $desa = \App\Models\Desa::where('kecamatan_id', $kecId)
                                        ->where('name', 'ILIKE', '%' . $desaName . '%')
                                        ->first();
                            if ($desa) {
                                $desaId = $desa->id;
                            }
                        }

                        // Fallback to first desa in kecamatan if empty or not found
                        if (!$desaId) {
                            $firstDesa = \App\Models\Desa::where('kecamatan_id', $kecId)->first();
                            if ($firstDesa) {
                                $desaId = $firstDesa->id;
                            }
                        }

                        if ($desaId) {
                            // Generate random date in that year
                            $month = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
                            $day = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
                            $date = $tahun . '-' . $month . '-' . $day;

                            // Random coordinate around Bekasi
                            $lat = -6.23 + (mt_rand(-100, 100) / 1000);
                            $lng = 107.0 + (mt_rand(-100, 100) / 1000);

                            \App\Models\LaporanBencana::create([
                                'desa_id' => $desaId,
                                'jenis_bencana_id' => $jenisId,
                                'title' => $jenisName . ' (' . $tahun . ')',
                                'description' => 'Laporan historis ' . $jenisName . ' tahun ' . $tahun,
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'date' => $date,
                                'status' => 'resolved', // Historical data usually resolved
                            ]);
                        }
                    }
                }
            }
        } else {
            // Fallback random seeders if CSV not found
            $jenis1 = \App\Models\JenisBencana::create(['name' => 'Banjir']);
            $jenis2 = \App\Models\JenisBencana::create(['name' => 'Tanah Longsor']);
            $jenis3 = \App\Models\JenisBencana::create(['name' => 'Puting Beliung']);

            if (count($kecamatans) > 0) {
                $desaIds = \App\Models\Desa::pluck('id')->toArray();
                $jenisIds = [$jenis1->id, $jenis2->id, $jenis3->id];

                for ($i = 0; $i < 15; $i++) {
                    $lat = -6.23 + (mt_rand(-100, 100) / 1000);
                    $lng = 107.0 + (mt_rand(-100, 100) / 1000);

                    \App\Models\LaporanBencana::create([
                        'desa_id' => $desaIds[array_rand($desaIds)],
                        'jenis_bencana_id' => $jenisIds[array_rand($jenisIds)],
                        'title' => 'Laporan Bencana ' . ($i + 1),
                        'description' => 'Ini adalah deskripsi dummy laporan bencana.',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'date' => now()->subDays(mt_rand(1, 15)),
                        'status' => (mt_rand(0, 1) == 1) ? 'active' : 'resolved',
                    ]);
                }
            }
        }
    }
}
