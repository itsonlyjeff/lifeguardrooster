<?php

namespace App\Filament\Commandcentre\Resources\UserResource\Pages;

use App\Filament\Commandcentre\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
