<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use App\Traits\HelpersTraits;
use App\Http\Requests\FilterRequest;

/**
 * @OA\Schema(
 *     schema="RoleResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Role successfully created"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="name", type="string", example="Tested Role"),
 *         @OA\Property(property="display_name", type="string", example="Displaying Tested Name"),
 *         @OA\Property(property="description", type="string", example="Lorem Ipsum Dolor Sit Amet"),
 *         @OA\Property(property="order", type="integer", example=15),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-19T14:01:33.000000Z"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-19T14:01:33.000000Z"),
 *         @OA\Property(property="id", type="integer", example=23)
 *     )
 * )
 */

class RoleController extends Controller
{
    use HelpersTraits;

    public function __construct(private RoleService $service)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/role/create",
     *     summary="Create a new role",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Role"},
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function store(RoleRequest $request)
    {
        return $this->service->createRole($request);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/role/{role_id}/update",
     *     summary="Update an existing role",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Role"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role to update"
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role successfully updated",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function update(RoleRequest $request, $role_id)
    {
        return $this->service->updateRole($request, $role_id);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/role/{role_id}/delete",
     *     summary="Delete an existing role",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Role"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role to delete"
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Role successfully deleted"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function destroy($id)
    {
        return $this->service->deleteRole($id);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/role",
     *     summary="Get all roles",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Role"},
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filters for the roles"
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Roles retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/RoleResponse")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function index(FilterRequest $request)
    {
        $filters = $request->filters();
        return $this->service->getAllRoles($filters);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/role/{role_id}",
     *     summary="Get a single role",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Role"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the role to retrieve"
     *     ),
     *     @OA\Parameter(
     *          name="Accept-Language",
     *          in="header",
     *          description="Language code for language selection",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              enum=LOCALE_ENUM,
     *              default="ko",
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function show($id)
    {
        return $this->service->showSingleRole($id);
    }
}
