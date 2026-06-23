<?php

namespace App\Filament\Resources\JenisBencanaResource\Pages;

use App\Filament\Resources\JenisBencanaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisBencana extends EditRecord
{
    protected static string $resource = JenisBencanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
