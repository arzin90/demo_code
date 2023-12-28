<?php

namespace App\Api\V1\Controllers;

use App\Constants\Status;
use App\Models\Page;

class PageController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function bySlug($slug)
    {
        $page = Page::query()->where(['status' => Status::ACTIVE, 'key' => $slug]);

        if ($page->exists()) {
            return $this->sendResponse($page->first());
        }

        return $this->sendError();
    }
}
