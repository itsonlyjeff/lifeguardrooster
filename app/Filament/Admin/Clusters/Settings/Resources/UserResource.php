<?php

namespace App\Filament\Admin\Clusters\Settings\Resources;

use App\Filament\Admin\Clusters\Settings;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SebastianBergmann\CodeCoverage\Driver\Selector;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam'),
                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Actief')
                    ->getStateUsing(function (User $user) {
                    return $user->tenants()->where('tenant_id', \Filament\Facades\Filament::getTenant()->id)->first()->pivot->is_active;
                }),
                Tables\Columns\BooleanColumn::make('is_admin')
                    ->label('Administrator')
                    ->getStateUsing(function (User $user) {
                        return $user->tenants()->where('tenant_id', \Filament\Facades\Filament::getTenant()->id)->first()->pivot->is_admin;
                    }),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('email_verified_at')->label('Email bevestigd op')->dateTime('d-m-Y H:i:s'),
                Tables\Columns\TextColumn::make('iban_tnv')->label('Tenaamstelling'),
                Tables\Columns\TextColumn::make('masked_iban')->label('IBAN'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
//            \App\Filament\Admin\Clusters\Settings\Resources\UserResource\RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Clusters\Settings\Resources\UserResource\Pages\ListUsers::route('/'),
            'view' => \App\Filament\Admin\Clusters\Settings\Resources\UserResource\Pages\ViewUser::route('/{record}'),
        ];
    }
}
