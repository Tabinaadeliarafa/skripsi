<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminExportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string $view = 'filament.pages.admin-export-page';

    protected static ?string $slug = 'export-data';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Export Data';
    }
}