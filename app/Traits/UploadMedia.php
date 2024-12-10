<?php

namespace App\Traits;
use Plank\Mediable\Media;
use Illuminate\Support\Facades\Config;
use Plank\Mediable\Facades\MediaUploader;

trait UploadMedia
{
    public function uploadBulletinImage($file): Media
    {
        try {

            return MediaUploader::fromSource($file)
                ->toDisk(Config::get('media.drive'))
                ->toDirectory('bulletin')
                ->useHashForFilename()
                ->withOptions($this->getOptions())
                ->upload();

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    private function getOptions()
    {
        return ['visibility' => config('filesystems.disks.' . env('FILESYSTEM_DRIVER') . '.visibility', 'public')];
    }

}
