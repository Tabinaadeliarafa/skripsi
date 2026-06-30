<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminPetaPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static string $view = 'filament.pages.admin-peta-page';

    protected static ?string $slug = 'peta';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Peta';
    }
}