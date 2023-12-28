<?php

namespace App\Api\V1\Controllers;

use App\Models\News;

class NewsController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendResponse(News::query()->where(['status_id' => News::STATUS_ACTIVE])->paginate(10));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $news = News::query()->where('id', $id)->where(['status_id' => News::STATUS_ACTIVE]);

        if ($news->exists()) {
            return $this->sendResponse($news->first());
        }

        return $this->sendError(['message' => 'Не найден']);
    }
}
