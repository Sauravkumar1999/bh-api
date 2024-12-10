<?php

namespace App\Services;

use App\Models\Bulletin;
use App\Traits\UploadMedia;
use Illuminate\Support\Facades\Auth;

class BulletinService
{
    use UploadMedia;
    private $model;

    public function __construct(Bulletin $bulletin)
    {
        $this->model = $bulletin;
    }

    public function createBulletin($request)
    {
        try {

            $bulletin = Bulletin::create([
                //'id' => $request->id,
                'title' => $request->title,
                'distinguish' => $request->distinguish,
                'attachment' => $request->attachment,
                'permission' => $request->permission ? json_encode($request->permission) : null,
                'content' => $request->content,
                'user_id' => auth()->user()->id,
                'type'    => $request->type,
                'due_date'=> $request->due_date,
                'view_count' => $request->view_count
            ]);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                try {
                    $media = $this->uploadBulletinImage($file);
                    $bulletin->syncMedia($media, 'attachment');
                    return $bulletin;
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }
            return $bulletin;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateBulletin($request, $id)
    {
        try {
            $bulletin = $this->model->findOrFail($id);
            $bulletin->title = $request->title;
            $bulletin->distinguish = $request->distinguish;
            $bulletin->attachment = $request->attachment;
            $bulletin->permission = $request->permission? json_encode($request->permission) : null;
            $bulletin->content = $request->content;
            $bulletin->user_id = auth()->user()->id;
            $update = $bulletin->save();
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                try {
                    $media = $this->uploadBulletinImage($file);
                    $bulletin->syncMedia($media, 'attachment');
                    return $bulletin;
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }
            return $bulletin;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function deleteBulletin($id)
    {
        try {
            $bulletin = $this->model->findOrFail($id);
            $bulletin->user_id = Auth::user()->id;
            $bulletin->deleted_at = now();
            $data = $bulletin->save();
            return [
                'status' => 'success',
                'data'   => 'Record Deleted succesfully'
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'data'    => 'Bulletin not found'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'data'    => $e->getMessage()
            ];
        }
    }



}
