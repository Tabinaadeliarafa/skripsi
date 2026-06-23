<?php
use App\Models\JenisBencana;
use App\Models\LaporanBencana;

$banjir = JenisBencana::firstOrCreate(['name' => 'Banjir']);
$kekeringan = JenisBencana::firstOrCreate(['name' => 'Kekeringan']);
$cuaca = JenisBencana::firstOrCreate(['name' => 'Cuaca Ekstrem']);

$allowedIds = [$banjir->id, $kekeringan->id, $cuaca->id];

$laporans = LaporanBencana::with('jenisBencana')->get();
$deletedCount = 0;
$updatedCount = 0;

foreach($laporans as $laporan) {
    if (!$laporan->jenisBencana) continue;

    $name = strtolower($laporan->jenisBencana->name);

    if (str_contains($name, 'banjir') || str_contains($name, 'rob')) {
        $laporan->jenis_bencana_id = $banjir->id;
        $laporan->save();
        $updatedCount++;
    } elseif (str_contains($name, 'kering')) {
        $laporan->jenis_bencana_id = $kekeringan->id;
        $laporan->save();
        $updatedCount++;
    } elseif (str_contains($name, 'angin') || str_contains($name, 'puting') || str_contains($name, 'cuaca') || str_contains($name, 'pohon') || str_contains($name, 'pasang') || str_contains($name, 'exstrim') || str_contains($name, 'ekstrim')) {
        $laporan->jenis_bencana_id = $cuaca->id;
        $laporan->save();
        $updatedCount++;
    } else {
        // If it's Longsor, Gempa, Orang Tenggelam, etc, we delete because user explicitly requested ONLY 3 types.
        $laporan->delete();
        $deletedCount++;
    }
}

// Clean up unused JenisBencana
$deletedJenis = JenisBencana::whereNotIn('id', $allowedIds)->delete();

echo "Updated $updatedCount reports. Deleted $deletedCount reports. Cleaned $deletedJenis redundant categories.\n";
