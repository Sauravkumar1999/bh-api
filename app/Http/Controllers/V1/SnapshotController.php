<?php

namespace App\Http\Controllers\V1;

use App\Traits\HelpersTraits;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Spatie\DbSnapshots\Events\CreatedSnapshot;

class SnapshotController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/snapshot/create",
     *     summary="Create new DB snapshot",
     *     security={{ "bearerAuth": {} }},
     *     tags={"Snapshot"},
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
     *     @OA\Parameter(
     *         name="excludes[]",
     *         in="query",
     *         description="tables need to exclude from snapshot",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string"))
     *     ),
     *     @OA\Parameter(
     *         name="includes[]",
     *         in="query",
     *         description="tables need to include into snapshot",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string"))
     *     ),
     *     @OA\Parameter(
     *         name="gzip",
     *         in="query",
     *         required=true,
     *         description="Compress the snapshot",
     *         @OA\Schema(type="boolean", default="false")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Snapshot successfully created",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function makeSnapshot(Request $request)
    {
        if (!is_admin_user()) {
            return HelpersTraits::sendError('', 'Unauthorized request !', 401);
        }

        if ($request->has('excludes') && $request->has('includes')) {
            return HelpersTraits::sendError('', 'Not allowed both includes and excludes at one go.', 500);
        }

        try {

            $command = "snapshot:create";

            if ($request->has('includes')) {
                $includes = $request->get('includes');
                array_walk($includes, fn(&$x) => $x = " --table=$x");
                $command .= implode('', $includes);
            }

            if ($request->has('excludes')) {
                $excludes = $request->get('excludes');
                array_walk($excludes, fn(&$x) => $x = " --exclude=$x");
                $command .= implode('', $excludes);
            }

            if ($request->has('gzip') && $request->boolean('gzip')) {
                $command .= ' --compress';
            }

            Artisan::call($command);

            return HelpersTraits::sendResponse('', 'Snapshot created successfully !');
        } catch (\Exception $e) {
            return HelpersTraits::sendError($e->getMessage());
        }
    }
}
