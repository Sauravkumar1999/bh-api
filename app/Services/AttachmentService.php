<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MediaHandlerTraits;
use Plank\Mediable\Media;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AttachmentService
{
    use MediaHandlerTraits;

    private $uploadMethod;

    public function __construct(string $uploadMethod = 'uploadGeneric')
    {
        $this->uploadMethod = $uploadMethod;
    }

    public function attachMedia(Model $model, array $data, string $collection)
    {
        try {
            $media = $this->uploadAndFetchMedia($data);
            if($media && !is_null($media)){
                $attachment = Media::whereBasename($media->basename)->first();
                $model->attachMedia($attachment, $collection);
                return $media;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function syncAttchment(Model $model, array $data, string $collection)
    {
        DB::beginTransaction();
        try {
            // Handle media replacement if `existing_attachment` and `attachment` are provided
            if (isset($data['existing_attachment']) && isset($data['attachment'])) {
                $existingMedia = Media::where('filename', $data['existing_attachment'])->first();              
                if ($existingMedia) {
                    $media = $this->swapMedia($existingMedia, $data['attachment']);
                }
            }

            // Handle new attachment
            if (!isset($data['existing_attachment']) && isset($data['attachment'])) {
                $media = $this->uploadAndFetchMedia($data);
            }

            DB::commit();
            return $media;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }


    private function uploadAndFetchMedia(array $data)
    {
        $media = $this->upload($data);
        if ($media instanceof Media) {
            return $media;
        }
        return null;
    }

    private function upload(array $data): Media
    {
        $file = $data['attachment'];
        return $this->{$this->uploadMethod}($file);
    }
}

