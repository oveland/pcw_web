<?php


namespace App\Services\Reports\Users;


use App\Models\Company\Company;
use App\Models\Reports\Activity\IgnoreUrl;
use App\Models\Reports\Activity\ActivityLog;
use FontLib\TrueType\Collection;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * @param Request $request
     */
    static function log(Request $request)
    {
        $request = self::filter($request);

        if ($request) {
            $segments = collect($request->segments());
            $routeName = self::getRouteName($request);

            $log = new ActivityLog([
                'route_name' => $routeName,
                'category1' => $segments->first(),
                'category2' => $segments->get(1),
                'category3' => $segments->get(2),
                'params' => collect($request->all())->toJson(),
                'url' => $request->url(),
                'method' => $request->method(),
                'agent' => $request->userAgent(),
            ]);

            $log->user()->associate(auth()->user());

            $log->save();
        }
    }

    /**
     * @param Request $request
     * @return Request
     */
    private static function filter(Request $request)
    {
        $ignoredRoute = IgnoreUrl::where('url', self::getRouteName($request))->first();

        $segments = collect($request->segments());

        if (!$ignoredRoute && $segments->first() != 'link') {
            return $request;
        }

        return null;
    }

    private static function getRouteName(Request $request)
    {
        $laravelRoute = collect(\Route::getRoutes()->getRoutesByName())->filter(function ($r, $routeName) use ($request) {
            return $request->routeIs($routeName);
        });

        return $laravelRoute->keys()->first();
    }

    /**
     * @param Company $company
     * @param $dateStart
     * @param null $dateEnd
     * @param null $user
     * @return ActivityLog[] | Collection
     */
    public function report(Company $company, $dateStart, $dateEnd = null, $user = null)
    {
        return ActivityLog::whereDateRangeAndUser($dateStart, $dateEnd, $user)
            ->where('route_name', '<>', 'null')
            ->whereIn('user_id', $company->users)
            ->orderBy('created_at')
            ->get();
    }
}