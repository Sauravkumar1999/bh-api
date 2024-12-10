<?php

namespace App\Traits;

use Modules\User\Entities\User;
use Illuminate\Support\Facades\Config;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;

trait MediaHandler
{
    /**
     * @param $file
     * @return Media
     * @throws \Plank\Mediable\Exceptions\MediaUpload\ConfigurationException
     * @throws \Plank\Mediable\Exceptions\MediaUpload\FileExistsException
     * @throws \Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException
     * @throws \Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException
     * @throws \Plank\Mediable\Exceptions\MediaUpload\FileSizeException
     * @throws \Plank\Mediable\Exceptions\MediaUpload\ForbiddenException
     */

    public function uploadBannerImage($file): Media
    {
        return MediaUploader::fromSource($file)
            ->toDisk(Config::get('media.drive'))
            ->toDirectory('banner')
            ->useHashForFilename()
            ->withOptions($this->getOptions())
            ->upload();
    }

    public function uploadSalesPerson($file): Media
    {
        $media = MediaUploader::fromSource($file)
            ->toDisk(Config::get('media.drive'))
            ->toDirectory('sales-person')
            ->useHashForFilename()
            ->withOptions($this->getOptions())
            ->upload();
        return $media;
    }

    private function getOptions()
    {
        return ['visibility' => config('filesystems.disks.' . env('FILESYSTEM_DRIVER') . '.visibility', 'public')];
    }
}
