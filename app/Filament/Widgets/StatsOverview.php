<?php

namespace App\Filament\Widgets;

use App\Models\LaporanBencana;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = LaporanBencana::count();
        $banjir = LaporanBencana::whereHas('jenisBencana', function ($q) {
            $q->where('name', 'Banjir');
        })->count();
        $kekeringan = LaporanBencana::whereHas('jenisBencana', function ($q) {
            $q->where('name', 'Kekeringan');
        })->count();
        $cuaca = LaporanBencana::whereHas('jenisBencana', function ($q) {
            $q->whereIn('name', ['Cuaca Ekstrem', 'Angin Puting Beliung']);
        })->count();

        return [
            Stat::make('Total Kejadian', $total)
                ->description('Seluruh data laporan')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
            Stat::make('Banjir', $banjir)
                ->description('Total kejadian banjir')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),
            Stat::make('Kekeringan', $kekeringan)
                ->description('Total kejadian kekeringan')
                ->descriptionIcon('heroicon-m-sun')
                ->color('warning'),
            Stat::make('Cuaca Ekstrem', $cuaca)
                ->description('Angin & Cuaca Ekstrem')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('danger'),
        ];
    }
}
