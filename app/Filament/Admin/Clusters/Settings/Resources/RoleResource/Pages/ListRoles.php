<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\RoleResource\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}