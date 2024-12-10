<?php

use App\Models\Channel;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('module_routes')) {

    function module_routes($module_dir = 'routes/api'): array
    {

        $iterator = new DirectoryIterator(base_path($module_dir));

        $module_routes = [];

        foreach ($iterator as $file) {

            if (!$file->isDot()) {

                $path = base_path($module_dir . '/' . $file->getFilename());

                if (file_exists($path)) {
                    $module_routes[] = base_path($module_dir . '/' . $file->getFilename());
                }
            }
        }

        return $module_routes;
    }
}

if (!function_exists('price_formatter')) {
    /*
     * Function is used to format number
     *
     */
    function price_formatter($number, $decimal = false, $decimal_separator = ".", $thousands_separator = ",")
    {
        return number_format((float)$number, $decimal, $decimal_separator, $thousands_separator);
    }
}

if (!function_exists('is_admin_user')) {

    /**
     * Check logged user or user passed via URL is admin
     *
     * @return boolean
     */
    function is_admin_user()
    {
        if (auth()->check()) {
            return user()->isAdmin();
        } else {
            $user = \App\Models\User::whereCode(last(request()->segments()));
            return $user->exists() ? $user->first()->hasRole('admin|developer|chief') : false;
        }
    }
}

if (!function_exists('user')) {
    /**
     * Get the authenticated user.
     *
     * @return \App\Models\User
     */
    function user()
    {
        // Get user from api/web
        if (request()->is('api/*')) {
            $user = auth()->guard('api')->user();
        } else {
            $user = auth()->user();
        }

        return $user;
    }
}
if (!function_exists('isSecure')) {

    function isSecure()
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }

        return $isSecure;
    }
}

if (!function_exists('sanitizeNumber')) {

    function sanitizeNumber($number)
    {
        $phone_number = preg_replace("/[^0-9]/", "", $number);


        return $phone_number;
    }
}

if (!function_exists('filterAndPagination')) {

    function filterAndPagination($query, $filters)
    {
        $perPage = $filters['per_page'] ?? config('erp.RECORDS_PER_PAGE');
        unset($filters['per_page']);

        $totalRecords = $query->filter($filters)->count();
        $paginatedData = $query->simplePaginate($perPage)->appends($filters + ['per_page' => $perPage]);

        $paginatedData->total_records = $totalRecords;

        return $paginatedData;
    }
}
if (!function_exists('assetUrl')) {

    function assetUrl($asset)
    {
        if (env("AWS_ROOT_DIR") != '') {
            return env("AWS_CDN") . '/' . env("AWS_ROOT_DIR") . '/' . $asset;
        } else {
            return env("AWS_CDN") . '/' . $asset;
        }
    }
}
if (!function_exists('mediaUrl')) {

    function mediaUrl($media, $default = '')
    {
        if ($media) {
            return assetUrl($media->getDiskPath());
        }
        return $default;
    }
}

if (!function_exists('merge_all_channel_data')) {
    function merge_all_channel_data(\Illuminate\Database\Eloquent\Collection $channelUserSettings)
    {
        return Channel::all()->collect()->map(function (Channel $channel) use ($channelUserSettings) {
            if ($channelUserSettings->where('id', $channel->id)->isEmpty()) {
                return array_merge($channel->toArray(), [
                    'channel_url'    => '',
                    'channel_status' => 1,
                ]);
            } else {
                $channelSetting = $channelUserSettings->where('id', $channel->id)->first();
                return array_merge($channel->toArray(), [
                    'channel_url'    => $channelSetting->pivot->url,
                    'channel_status' => $channelSetting->pivot->status,
                ]);
            }
        });
    }
}

if (!function_exists('merge_user_settings')) {

    /**
     * Merge user settings into the member company channel listing
     *
     * @param User $user
     * @param \Illuminate\Database\Eloquent\Collection $channels
     * @return \Illuminate\Support\Collection
     */

    function merge_user_settings(User $user,  $channels)
    {
        $max_channel_order = 0;

        return $channels->map(function ($channel) use ($user, &$max_channel_order) {

            if (!empty($channel->url_params)) {

                $urlService = new App\Services\ChannelURLGeneratorService();
                $main_url = isset($channel->main_url) ? $urlService->createURL($channel, $user) : '';
            } else {

                $main_url = isset($channel->main_url) ? $channel->main_url . '?bhid=' . $user->code : '';
            }

            if (($user->userSetting && $user->userSetting->channel_ordering) && in_array($channel->id, array_column($user->userSetting->channel_ordering, 'channel_id'))) {

                $aData = current(\Illuminate\Support\Arr::where($user->userSetting->channel_ordering, function ($v, $k) use ($channel) {
                    return isset($v['channel_id']) ? $v['channel_id'] == $channel->id : null;
                }));

                $adminUserSettings = $channel->adminUserSettings()->wherePivot('user_id', $user->id);

                if ($adminUserSettings->exists()) {
                    $adminUserSetting = $adminUserSettings->first();
                    if (!empty($adminUserSetting->pivot->url) && $adminUserSetting->pivot->status) {
                        $main_url = $adminUserSetting->pivot->url;
                    }
                }


                return array_merge($channel->toArray(), [
                    'odr_app'  => $aData['order'] ?? 0,
                    'main_url' => $main_url,
                    'channel_image' => $channel->banner()
                ]);
            } else {

                if ($user->userSetting && $user->userSetting->channel_ordering) {
                    $cur_max = max(array_column($user->userSetting->channel_ordering, 'order')) ?? 0;

                    if ($cur_max > $max_channel_order) {
                        $max_channel_order = $cur_max;
                    }
                    $max_channel_order++;

                    // Update the channel_ordering array
                    $channeluct_ordering = $user?->userSetting?->channel_ordering ?? [];
                    $channeluct_ordering[] = [
                        'channel_id' => $channel->id,
                        'order'      => $max_channel_order,
                    ];

                    $user->userSetting->channel_ordering = $channeluct_ordering;
                    $user->userSetting->save();
                } else {
                    $max_channel_order = $channel->exposer_order;
                    if (is_null($max_channel_order)) {
                        // Get the maximum existing order value
                        $maxExistingOrder = \App\Models\Channel::max('exposer_order');

                        // Set the new order value to be one greater than the current maximum
                        $max_channel_order = $maxExistingOrder ? $maxExistingOrder + 1 : 1;
                    }

                }


                $adminUserSettings = $channel->adminUserSettings()->wherePivot('user_id', $user->id);

                if ($adminUserSettings->exists()) {
                    $adminUserSetting = $adminUserSettings->first();
                    if (!empty($adminUserSetting->pivot->url) && $adminUserSetting->pivot->status) {
                        $main_url = $adminUserSetting->pivot->url;
                    }
                }

                return array_merge($channel->toArray(), [
                    'odr_app'  => $max_channel_order ?? 0,
                    'exposure' => false,
                    'url'      => '',
                    'main_url' => $main_url,
                    'channel_image' => $channel->banner()
                ]);
            }
        })->sortBy('odr_app');
    }
}
if (!function_exists('is_royal_member')) {

    /**
     * Check logged user is a royal member
     *
     * @return boolean
     */
    function is_royal_member($user = '')
    {
        $currentUser = ($user == '') ? user() : $user;
        if ($currentUser && (now() >= $currentUser->start_date && now() <= $currentUser->end_date)) {
            return true;
        }
        return false;
    }

}

if(!function_exists('format_phone_number')){
    function format_phone_number($phone_number){
        $phone_number = preg_replace("/[^0-9]/", "", $phone_number);
        $phone_number = preg_replace("/^(\d{3})(\d{4})(\d{4})$/", "$1-$2-$3", $phone_number);
        return $phone_number;
    }
}
