<?php

namespace App\Filament\Commandcentre\Resources\TenantResource\Pages;

use App\Filament\Commandcentre\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
