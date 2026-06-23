<?php

namespace App\Filament\Widgets;

use App\Models\LaporanBencana;
use Filament\Widgets\ChartWidget;

class BencanaWilayahChart extends ChartWidget
{
    protected static ?string $heading = 'Kejadian per Kecamatan (Top 10)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = LaporanBencana::with('desa.kecamatan')->get()
            ->groupBy(function($item) {
                return ($item->desa && $item->desa->kecamatan) ? $item->desa->kecamatan->name : 'Lainnya';
            })->map->count()
            ->sortDesc()
            ->take(10);

        return [
            'datasets' => [
                [
                    'label' => 'Total Kejadian',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => '#D45B1F',
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
