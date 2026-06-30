<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminStatistikPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.admin-statistik-page';

    protected static ?string $slug = 'statistik-analisis';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Statistik & Analisis';
    }
}