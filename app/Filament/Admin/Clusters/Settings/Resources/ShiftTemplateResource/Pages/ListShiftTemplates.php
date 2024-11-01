<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShiftTemplates extends ListRecords
{
    protected static string $resource = ShiftTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
