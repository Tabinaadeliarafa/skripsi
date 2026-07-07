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
        if (env('IMPORT_2023_2024_ONLY', false)) {
            $this->importDataBencana2023Dan2024();
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

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
                if ($indeks <= 0.333) {
                    $color = '#10b981'; // Green
                } elseif ($indeks <= 0.666) {
                    $color = '#f59e0b'; // Amber
                } else {
                    $color = '#ef4444'; // Red
                }

                $kecamatan = \App\Models\Kecamatan::updateOrCreate(
                    ['name' => $name],
                    [
                        'color_code' => $color,
                        'indeks_bahaya' => $indeks,
                        'geojson' => json_encode($feature),
                    ]
                );

                $kecamatans[] = $kecamatan;
                $kecamatanMap[strtoupper(trim($name))] = $kecamatan->id;
            }
        }

        // Seed Desas from desa.geojson
        $desaGeoJsonString = file_get_contents(public_path('geo/desa.geojson'));
        $desaGeoData = json_decode($desaGeoJsonString, true);

        if (isset($desaGeoData['features'])) {
            foreach ($desaGeoData['features'] as $feature) {
                $kecamatanName = strtoupper(trim($feature['properties']['NAME_3'] ?? ''));
                $desaName = $feature['properties']['NAME_4'] ?? 'Unknown';

                if (isset($kecamatanMap[$kecamatanName])) {
                    \App\Models\Desa::updateOrCreate(
                        [
                            'kecamatan_id' => $kecamatanMap[$kecamatanName],
                            'name' => $desaName,
                        ],
                        [
                            'geojson' => json_encode($feature),
                        ]
                    );
                }
            }
        }

        // Read Laporan Bencana from CSV utama
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
                    } elseif (
                        str_contains($jenisNameLower, 'angin') ||
                        str_contains($jenisNameLower, 'puting') ||
                        str_contains($jenisNameLower, 'cuaca') ||
                        str_contains($jenisNameLower, 'pohon') ||
                        str_contains($jenisNameLower, 'pasang') ||
                        str_contains($jenisNameLower, 'ekstrim') ||
                        str_contains($jenisNameLower, 'exstrim')
                    ) {
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
                            $lat = round(-6.23 + (mt_rand(-100, 100) / 1000), 6);
                            $lng = round(107.0 + (mt_rand(-100, 100) / 1000), 6);

                            \App\Models\LaporanBencana::updateOrCreate(
                                [
                                    'desa_id' => $desaId,
                                    'jenis_bencana_id' => $jenisId,
                                    'title' => $jenisName . ' (' . $tahun . ')',
                                    'date' => $date,
                                ],
                                [
                                    'description' => 'Laporan historis ' . $jenisName . ' tahun ' . $tahun,
                                    'latitude' => $lat,
                                    'longitude' => $lng,
                                    'status' => 'resolved',
                                ]
                            );
                        }
                    }
                }
            }
        } else {
            // Fallback random seeders if CSV not found
            $jenis1 = \App\Models\JenisBencana::firstOrCreate(['name' => 'Banjir']);
            $jenis2 = \App\Models\JenisBencana::firstOrCreate(['name' => 'Tanah Longsor']);
            $jenis3 = \App\Models\JenisBencana::firstOrCreate(['name' => 'Puting Beliung']);

            if (count($kecamatans) > 0) {
                $desaIds = \App\Models\Desa::pluck('id')->toArray();
                $jenisIds = [$jenis1->id, $jenis2->id, $jenis3->id];

                for ($i = 0; $i < 15; $i++) {
                    $lat = round(-6.23 + (mt_rand(-100, 100) / 1000), 6);
                    $lng = round(107.0 + (mt_rand(-100, 100) / 1000), 6);

                    \App\Models\LaporanBencana::updateOrCreate(
                        [
                            'title' => 'Laporan Bencana ' . ($i + 1),
                            'date' => now()->subDays(mt_rand(1, 15))->format('Y-m-d'),
                        ],
                        [
                            'desa_id' => $desaIds[array_rand($desaIds)],
                            'jenis_bencana_id' => $jenisIds[array_rand($jenisIds)],
                            'description' => 'Ini adalah deskripsi dummy laporan bencana.',
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'status' => (mt_rand(0, 1) == 1) ? 'active' : 'resolved',
                        ]
                    );
                }
            }
        }

        // =====================================================
        // IMPORT TAMBAHAN DATA BENCANA 2023 DAN 2024
        // =====================================================
        $csvTambahan = public_path('data/2023-2024(update).csv');

        if (file_exists($csvTambahan)) {
            $file = fopen($csvTambahan, 'r');

            $header = fgetcsv($file, 0, ';');

            // Menghapus BOM pada header pertama jika ada
            if (isset($header[0])) {
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            }

            $importedTambahan = 0;
            $skippedTambahan = [];

            while (($row = fgetcsv($file, 0, ';')) !== false) {
                if (count($row) !== count($header)) {
                    $skippedTambahan[] = [
                        'reason' => 'Jumlah kolom tidak sesuai',
                        'row' => $row,
                    ];
                    continue;
                }

                $data = array_combine($header, $row);

                $jenisNameLower = strtolower(trim($data['Jenis Kejadian'] ?? ''));
                $tahun = trim($data['Tahun Kejadian'] ?? '');
                $kecamatanName = strtoupper(trim($data['Kecamatan'] ?? ''));
                $aliasKecamatan = [
                    'MUARAGEMBONG' => 'MUARA GEMBONG',
                ];

                if (isset($aliasKecamatan[$kecamatanName])) {
                    $kecamatanName = $aliasKecamatan[$kecamatanName];
                }
                $desaName = strtoupper(trim($data['Desa'] ?? ''));

                if (!in_array($tahun, ['2023', '2024'])) {
                    continue;
                }

                if ($jenisNameLower === '' || $tahun === '' || $kecamatanName === '' || $desaName === '') {
                    $skippedTambahan[] = [
                        'reason' => 'Jenis/Tahun/Kecamatan/Desa kosong',
                        'data' => $data,
                    ];
                    continue;
                }

                if (str_contains($jenisNameLower, 'banjir') || str_contains($jenisNameLower, 'rob')) {
                    $jenisName = 'Banjir';
                } elseif (str_contains($jenisNameLower, 'kering')) {
                    $jenisName = 'Kekeringan';
                } elseif (
                    str_contains($jenisNameLower, 'angin') ||
                    str_contains($jenisNameLower, 'puting') ||
                    str_contains($jenisNameLower, 'cuaca') ||
                    str_contains($jenisNameLower, 'pohon') ||
                    str_contains($jenisNameLower, 'pasang') ||
                    str_contains($jenisNameLower, 'ekstrim') ||
                    str_contains($jenisNameLower, 'exstrim') ||
                    str_contains($jenisNameLower, 'ekstrem')
                ) {
                    $jenisName = 'Cuaca Ekstrem';
                } else {
                    $skippedTambahan[] = [
                        'reason' => 'Jenis bencana tidak dikenali',
                        'jenis' => $jenisNameLower,
                        'data' => $data,
                    ];
                    continue;
                }

                $jenis = \App\Models\JenisBencana::firstOrCreate([
                    'name' => $jenisName,
                ]);

                $kecamatan = \App\Models\Kecamatan::whereRaw('UPPER(TRIM(name)) = ?', [
                    $kecamatanName,
                ])->first();

                if (!$kecamatan) {
                    $kecamatan = \App\Models\Kecamatan::where('name', 'ILIKE', '%' . $kecamatanName . '%')->first();
                }

                if (!$kecamatan) {
                    $skippedTambahan[] = [
                        'reason' => 'Kecamatan tidak ditemukan',
                        'kecamatan' => $kecamatanName,
                        'data' => $data,
                    ];
                    continue;
                }

                $desa = \App\Models\Desa::where('kecamatan_id', $kecamatan->id)
                    ->whereRaw('UPPER(TRIM(name)) = ?', [$desaName])
                    ->first();

                if (!$desa) {
                    $desa = \App\Models\Desa::where('kecamatan_id', $kecamatan->id)
                        ->where('name', 'ILIKE', '%' . $desaName . '%')
                        ->first();
                }

                if (!$desa) {
                    $desa = \App\Models\Desa::whereRaw('UPPER(TRIM(name)) = ?', [$desaName])->first();
                }

                if (!$desa) {
                    $desa = \App\Models\Desa::create([
                        'kecamatan_id' => $kecamatan->id,
                        'name' => ucwords(strtolower($desaName)),
                        'geojson' => null,
                    ]);
                }

                if (empty($desa->kecamatan_id)) {
                    $desa->update([
                        'kecamatan_id' => $kecamatan->id,
                    ]);
                }

                $month = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
                $day = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
                $date = $tahun . '-' . $month . '-' . $day;

                $lat = round(-6.23 + (mt_rand(-100, 100) / 1000), 6);
                $lng = round(107.0 + (mt_rand(-100, 100) / 1000), 6);

                \App\Models\LaporanBencana::create([
                    'desa_id' => $desa->id,
                    'jenis_bencana_id' => $jenis->id,
                    'title' => $jenisName . ' (' . $tahun . ')',
                    'description' => 'Laporan historis ' . $jenisName . ' tahun ' . $tahun,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'date' => $date,
                    'status' => 'resolved',
                ]);

                $importedTambahan++;
            }

            fclose($file);

            echo "Import tambahan 2023 dan 2024 selesai.\n";
            echo "Total data masuk: {$importedTambahan}\n";
            echo "Total data dilewati: " . count($skippedTambahan) . "\n";

            if (count($skippedTambahan) > 0) {
                dump($skippedTambahan);
            }
        } else {
            echo "File 2023-2024(update).csv tidak ditemukan di public/data/\n";
        }
    }

    private function importDataBencana2023Dan2024(): void
    {
        $csvTambahan = database_path('data/2023-2024(update).csv');

        if (! file_exists($csvTambahan)) {
            echo "File 2023-2024(update).csv tidak ditemukan di public/data/\n";
            return;
        }

        // Hapus dulu data 2023 dan 2024 agar tidak dobel saat seed ulang
        \App\Models\LaporanBencana::whereYear('date', 2023)
            ->orWhereYear('date', 2024)
            ->delete();

        $file = fopen($csvTambahan, 'r');

        $header = fgetcsv($file, 0, ';');

        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        $importedTambahan = 0;
        $skippedTambahan = [];

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            if (count($row) !== count($header)) {
                $skippedTambahan[] = [
                    'reason' => 'Jumlah kolom tidak sesuai',
                    'row' => $row,
                ];
                continue;
            }

            $data = array_combine($header, $row);

            $jenisNameLower = strtolower(trim($data['Jenis Kejadian'] ?? ''));
            $tahun = trim($data['Tahun Kejadian'] ?? '');
            $kecamatanName = strtoupper(trim($data['Kecamatan'] ?? ''));
            $desaName = strtoupper(trim($data['Desa'] ?? ''));

            $aliasKecamatan = [
                'MUARAGEMBONG' => 'MUARA GEMBONG',
            ];

            if (isset($aliasKecamatan[$kecamatanName])) {
                $kecamatanName = $aliasKecamatan[$kecamatanName];
            }

            if (! in_array($tahun, ['2023', '2024'])) {
                continue;
            }

            if ($jenisNameLower === '' || $tahun === '' || $kecamatanName === '' || $desaName === '') {
                $skippedTambahan[] = [
                    'reason' => 'Jenis/Tahun/Kecamatan/Desa kosong',
                    'data' => $data,
                ];
                continue;
            }

            if (str_contains($jenisNameLower, 'banjir') || str_contains($jenisNameLower, 'rob')) {
                $jenisName = 'Banjir';
            } elseif (str_contains($jenisNameLower, 'kering')) {
                $jenisName = 'Kekeringan';
            } elseif (
                str_contains($jenisNameLower, 'angin') ||
                str_contains($jenisNameLower, 'puting') ||
                str_contains($jenisNameLower, 'cuaca') ||
                str_contains($jenisNameLower, 'pohon') ||
                str_contains($jenisNameLower, 'pasang') ||
                str_contains($jenisNameLower, 'ekstrim') ||
                str_contains($jenisNameLower, 'exstrim') ||
                str_contains($jenisNameLower, 'ekstrem')
            ) {
                $jenisName = 'Cuaca Ekstrem';
            } else {
                $skippedTambahan[] = [
                    'reason' => 'Jenis bencana tidak digunakan pada sistem',
                    'jenis' => $jenisNameLower,
                    'data' => $data,
                ];
                continue;
            }

            $jenis = \App\Models\JenisBencana::firstOrCreate([
                'name' => $jenisName,
            ]);

            $kecamatan = \App\Models\Kecamatan::whereRaw('UPPER(TRIM(name)) = ?', [
                $kecamatanName,
            ])->first();

            if (! $kecamatan) {
                $kecamatan = \App\Models\Kecamatan::where('name', 'ILIKE', '%' . $kecamatanName . '%')->first();
            }

            if (! $kecamatan) {
                $skippedTambahan[] = [
                    'reason' => 'Kecamatan tidak ditemukan',
                    'kecamatan' => $kecamatanName,
                    'data' => $data,
                ];
                continue;
            }

            $desa = \App\Models\Desa::where('kecamatan_id', $kecamatan->id)
                ->whereRaw('UPPER(TRIM(name)) = ?', [$desaName])
                ->first();

            if (! $desa) {
                $desa = \App\Models\Desa::where('kecamatan_id', $kecamatan->id)
                    ->where('name', 'ILIKE', '%' . $desaName . '%')
                    ->first();
            }

            if (! $desa) {
                $desa = \App\Models\Desa::whereRaw('UPPER(TRIM(name)) = ?', [$desaName])->first();
            }

            if (! $desa) {
                $desa = \App\Models\Desa::create([
                    'kecamatan_id' => $kecamatan->id,
                    'name' => ucwords(strtolower($desaName)),
                    'geojson' => null,
                ]);
            }

            if (empty($desa->kecamatan_id)) {
                $desa->update([
                    'kecamatan_id' => $kecamatan->id,
                ]);
            }

            $month = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
            $day = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
            $date = $tahun . '-' . $month . '-' . $day;

            $lat = round(-6.23 + (mt_rand(-100, 100) / 1000), 6);
            $lng = round(107.0 + (mt_rand(-100, 100) / 1000), 6);

            \App\Models\LaporanBencana::create([
                'desa_id' => $desa->id,
                'jenis_bencana_id' => $jenis->id,
                'title' => $jenisName . ' (' . $tahun . ')',
                'description' => 'Laporan historis ' . $jenisName . ' tahun ' . $tahun,
                'latitude' => $lat,
                'longitude' => $lng,
                'date' => $date,
                'status' => 'resolved',
            ]);

            $importedTambahan++;
        }

        fclose($file);

        echo "Import tambahan 2023 dan 2024 selesai.\n";
        echo "Total data masuk: {$importedTambahan}\n";
        echo "Total data dilewati: " . count($skippedTambahan) . "\n";

        if (count($skippedTambahan) > 0) {
            dump($skippedTambahan);
        }
    }
}