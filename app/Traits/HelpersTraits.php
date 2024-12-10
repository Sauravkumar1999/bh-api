<?php

    namespace App\Traits;
    use Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    trait HelpersTraits
    {

        /**
         * success response method.
         *
         * @return \Illuminate\Http\Response
         */
        public static function sendResponse($result,$message,$pagination = null) {
            $response = [
                'success' => true,
                'message' => $message,
                'data' => $result,
            ];
            if ($pagination) {
                if ($pagination instanceof LengthAwarePaginator) {
                    // Use LengthAwarePaginator methods
                    $totalRecords = $pagination->total();
                }else{
                    $totalRecords = $pagination->total_records;
                }

                $response += [
                    'pagination' => [
                        'current_page' => $pagination->currentPage(),
                        'first_page_url' => $pagination->url(1),
                        'from' => $pagination->firstItem(),
                        'next_page_url' => $pagination->nextPageUrl(),
                        'path' => $pagination->path(),
                        'per_page' => (int) $pagination->perPage(),
                        'prev_page_url' => $pagination->previousPageUrl(),
                        'to' => $pagination->lastItem(),
                        'total' => $totalRecords,
                        'total_pages'=> ceil($totalRecords / $pagination->perPage()),
                    ],
                ];
            }

            return response()->json($response, 200);
        }


        /**
         * return error response.
         *
         * @return \Illuminate\Http\Response
         */
        public static function sendError($error,$errorMessages = [],$code = 404)
        {
            $response = [
                'success' => false,
                'message' => $error,
                'data' => [],
            ];
            if (!empty($errorMessages)) {
                $response['data']['errors'] = $errorMessages;
            }

            return response()->json($response, $code);

        }
    }
