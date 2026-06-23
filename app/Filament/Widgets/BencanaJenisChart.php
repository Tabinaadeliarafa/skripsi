<?php

namespace App\Filament\Widgets;

use App\Models\LaporanBencana;
use Filament\Widgets\ChartWidget;

class BencanaJenisChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Bencana';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = LaporanBencana::with('jenisBencana')->get()
            ->groupBy(function($item) {
                return $item->jenisBencana ? $item->jenisBencana->name : 'Lainnya';
            })->map->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Kejadian',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => ['#D45B1F', '#f59e53', '#12395C', '#3b82f6', '#10b981', '#f43f5e'],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
