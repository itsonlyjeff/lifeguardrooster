<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadMediaController extends Controller
{
    public function downloadFile(Media $mediaItem): BinaryFileResponse
    {
        $attachedModel = $mediaItem->model; // Haal het gekoppelde model op (bijvoorbeeld een Shift)

        if (! Gate::allows('view', $attachedModel)) {
            abort(403, 'Je hebt geen toegang tot dit bestand.');
        }

        return response()->download($mediaItem->getPath(), $mediaItem->file_name);
    }
}
