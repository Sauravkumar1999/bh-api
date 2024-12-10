<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('locale')) {
            $locale = $request->get('locale');
        } elseif ($request->hasHeader('Accept-Language')) {
            $locale = $this->parseHttpLocale($request);
        } else {
            $locale = config('app.locale');
        }

        if (in_array($locale, config('app.available_locales'))) {
            App::setLocale($locale);
        }

        return $next($request);
    }


    private function parseHttpLocale(Request $request): string
    {
        $list = explode(',', $request->header('Accept-Language', ''));

        $locales = Collection::make($list)
            ->map(function ($locale) {
                $parts = explode(';', $locale);

                $mapping['locale'] = trim($parts[0]);

                if (isset($parts[1])) {
                    $factorParts = explode('=', $parts[1]);

                    $mapping['factor'] = $factorParts[1];
                } else {
                    $mapping['factor'] = 1;
                }

                return $mapping;
            })
            ->sortByDesc(function ($locale) {
                return $locale['factor'];
            });

        return $locales->first()['locale'];
    }
}
