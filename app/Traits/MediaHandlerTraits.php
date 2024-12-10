<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;
use Illuminate\Support\Facades\Storage;

trait MediaHandlerTraits
{
    public function uploadAllowancePayment($file): Media
    {
        return MediaUploader::fromSource($file)
            ->toDisk(Config::get('filesystems.default'))
            ->toDirectory('allowance-payment')
            ->useHashForFilename()
            ->withOptions($this->getOptions())
            ->upload();
    }

    public function uploadGeneric($file): Media
    {
        return MediaUploader::fromSource($file)
            ->toDisk(Config::get('filesystems.default'))
            ->toDirectory('generic-uploads')
            ->useHashForFilename()
            ->withOptions($this->getOptions())
            ->upload();
    }

    public function deleteFileFromStorage(Media $media)
    {
        $filePath = $media->getDiskPath();
        return Storage::disk($media->disk)
            ->delete($filePath);
      
    }

    public function swapMedia(Media $media, $file){
        return MediaUploader::fromSource($file)
            ->replace($media);
    }

    public function updateMedia(Media $media){
        return MediaUploader::update($media);
    }

    private function getOptions()
    {
        return ['visibility' => config('filesystems.disks.' . env('FILESYSTEM_DRIVER') . '.visibility', 'public')];
    }
    
}
