<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    private $model;

    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    public function createPermission($request)
    {
        try {
            $this->model->create($request->validated());
            return [
                'message' => 'permission created',
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updatePermission($request, $id)
    {
        try {
            $this->model->findOrFail($id)->update($request->validated());

            return [
                'message' => 'permission updated',
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deletePermission($id)
    {
        try {
            $this->model->findOrFail($id)->delete();

            return [
                'message' => 'permission deleted',
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
