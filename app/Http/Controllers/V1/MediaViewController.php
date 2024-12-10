<?php

namespace App\Http\Controllers\V1;

use Plank\Mediable\Media;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MediaViewController extends Controller
{

    public function getMediaURL(Media $media)
    {
        try {
            return response()->json([
                'img_url' => Storage::disk($media->disk)->temporaryUrl($media->getDiskPath(), now()->addMinutes(5))
            ]);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

    }

}
