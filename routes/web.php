<?php

use App\Filament\Pages\Auth\UserHasNoTenantSimplePage;
use App\Http\Controllers\DownloadMediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download/{mediaItem}', [DownloadMediaController::class, 'downloadFile'])
    ->middleware('auth')
    ->name('download');

Route::get('/tenant-missing', UserHasNoTenantSimplePage::class)->name('auth.tenant-missing');
