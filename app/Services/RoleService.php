<?php

namespace App\Services;

use Exception;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Traits\HelpersTraits;
use App\Http\Requests\RoleRequest;

class RoleService
{
    use HelpersTraits;

    private $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function getAllRoles($filters)
    {
        try {
            $roles = Role::orderBy('id', 'DESC')->withCount('users')->filterAndPaginate($filters);
            return $this->sendResponse(RoleResource::collection($roles), __('messages.roles_retrieved'), RoleResource::collection($roles));
        } catch (Exception $e) {
            return $this->sendError(__('messages.error_retrieving_roles'), ['error' => $e->getMessage()]);
        }
    }



    public function createRole(RoleRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $role = Role::create($validatedData);

            return $this->sendResponse(RoleResource::collection([$role]), __('messages.role_created'));
        } catch (Exception $e) {
            return $this->sendError(__('messages.error_creating_role'), ['error' => $e->getMessage()]);
        }
    }

    public function updateRole(RoleRequest $request, int $id)
    {
        try {
            $validatedData = $request->validated();
             // check of role id exists
            $role =  Role::find($id);
            if($role){
                // check if the role name is unique
                $role->update($validatedData);
                return $this->sendResponse(RoleResource::collection([$role]), __('messages.role_updated'));
            }

            return $this->sendError(__('messages.error_updating_role'), ['error' => 'Role not found']);
        } catch (Exception $e) {
            return $this->sendError(__('messages.error_updating_role'), ['error' => $e->getMessage()]);
        }
    }

    public function deleteRole(int $id)
    {
        try {

            $role =  Role::find($id);
            if($role){
                // check if the role name is unique
                $role->delete();
                return $this->sendResponse(null, __('messages.role_deleted'));
            }
            return $this->sendError(__('messages.error_deleting_role'), ['error' => 'Role not found']);
        } catch (Exception $e) {
            return $this->sendError(__('messages.error_deleting_role'), ['error' => $e->getMessage()]);
        }
    }

    public function showSingleRole(int $id)
    {
        try {
            $role = Role::findOrFail($id);
            return $this->sendResponse(RoleResource::collection([$role]), __('messages.role_retrieved'));
        } catch (Exception $e) {
            return $this->sendError(__('messages.error_retrieving_role'), ['error' => $e->getMessage()]);
        }
    }
}
