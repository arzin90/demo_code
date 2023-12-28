<?php

namespace App\Api\V1\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $s = $request->get('s');

        if ($s) {
            $locations = Location::query()->select(['id', 'city', 'popular', 'region'])->where(function($query) use ($s) {
                $query->orWhere('city', 'like', sprintf('%s%%', $s));
                $query->orWhere('city', 'like', sprintf('%%-%s%%', $s));
            })->orderBy('popular', 'desc')->orderBy('city')->paginate();
        } else {
            $locations = Location::query()->select(['id', 'city', 'popular', 'region'])->orderBy('popular', 'desc')->orderBy('city')->paginate();
        }

        return $this->sendResponse($locations);
    }
}
