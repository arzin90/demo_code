<?php

namespace App\Api\V1\Controllers;

use App\Constants\Status;
use App\Helper\Notification;
use App\Models\Chapter;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\ProgramChapter;
use App\Models\ProgramDate;
use App\Models\ProgramProgramCategory;
use App\Models\ProgramUser;
use App\Models\Specialist;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProgramController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $data = $request->only(['name', 'sort', 'order', 'is_online', 'location_ids', 'category_ids', 'specialist_ids', 'start_date', 'end_date', 'is_free']);

        $validator = Validator::make($data, [
            'name' => 'nullable|string|max:100',
            'sort' => 'nullable|string|in:name,date,rate,price,popular',
            'order' => 'nullable|string|in:asc,desc',
            // filters
            'is_online' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after:start_date',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'required|integer|exists:locations,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'required|integer|exists:program_categories,id',
            'specialist_ids' => 'nullable|array',
            'specialist_ids.*' => 'required|integer|exists:specialists,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sort = !empty($data['sort']) ? $data['sort'] : '';
        $order = !empty($data['order']) ? $data['order'] : '';

        $programs = Program::query()->where(['status' => Status::ACTIVE]);

        if (!empty($data['name'])) {
            $programs->where('name', 'like', sprintf('%%%s%%', $data['name']));
        }

//        $auth_id = auth()->id();

//        if (Specialist::query()->where(['user_id' => $auth_id, 'status' => Status::BLOCKED])->exists()) {
//            $programs->whereHas('specialist', function ($query) use ($auth_id) {
//                $query->where('user_id', '<>', $auth_id);
//            });
//        }

        if (array_key_exists('is_online', $data)) {
            $programs->where(['is_online' => $data['is_online']]);

            if (!$data['is_online']) {
                if (!empty($data['location_ids'])) {
                    $programs->whereIn('location_id', $data['location_ids']);
                } else {
                    $programs->whereNotNull('location_id');
                }
            }
        }

        if (!empty($data['is_free'])) {
            $programs->where(['price' => 0]);
        }

        if (!empty($data['category_ids'])) {
            $programs->whereHas('categories', function($query) use ($data) {
                $query->whereIn('program_category_id', $data['category_ids']);
            });
        }

        if (!empty($data['specialist_ids'])) {
            $programs->whereIn('specialist_id', $data['specialist_ids']);
        }

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $programs->whereHas('programDates', function($query) use ($data) {
                $query->whereBetween('date', [$data['start_date'], $data['end_date']]);
            });
        }

        $programs->with(['specialist.user', 'categories', 'programDates', 'programChapters', 'users', 'location']);

        if ($sort) {
            if ($sort == 'popular') {
                $programs->withCount(['users' => function($query) {
                    return $query->where('is_payed', '=', 1);
                }])->orderByDesc('users_count');
            } else {
                if ($order == 'desc') {
                    if ($sort == 'date') {
                        DB::select("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
                        $programDate = ProgramDate::query()->where('date', '>=', now())->orderByDesc('date')->orderByDesc('start_time')->groupBy('program_id');
                        $ids = $programDate->pluck('program_id')->toArray();

                        if (!empty($ids)) {
                            $ids_ordered = implode(',', $ids);
                            $programs->whereIn('programs.id', $ids);
                            $programs->orderByRaw("FIELD(programs.id, {$ids_ordered})");
                        }
                    } else {
                        $programs->orderByDesc($sort);
                    }
                } else {
                    if ($sort == 'date') {
                        DB::select("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
                        $programDate = ProgramDate::query()->where('date', '>=', now())->orderBy('date')->orderBy('start_time')->groupBy('program_id');
                        $ids = $programDate->pluck('program_id')->toArray();

                        if (!empty($ids)) {
                            $ids_ordered = implode(',', $ids);
                            $programs->whereIn('programs.id', $ids);
                            $programs->orderByRaw("FIELD(programs.id, {$ids_ordered})");
                        }
                    } else {
                        $programs->orderBy($sort);
                    }
                }
            }
        } else {
            $programs->latest();
        }

        return $this->sendResponse($programs->paginate(15));
    }

    /**
     * @return JsonResponse
     */
    public function my(Request $request)
    {
        $data = $request->only(['name', 'sort', 'order', 'is_online', 'location_ids', 'start_date', 'end_date', 'is_free']);

        $validator = Validator::make($data, [
            'name' => 'nullable|string|max:100',
            'sort' => 'nullable|string|in:name,date,rate,price',
            'order' => 'nullable|string|in:asc,desc',
            // filters
            'is_online' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after:start_date',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'required|integer|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sort = !empty($data['sort']) ? $data['sort'] : '';
        $order = !empty($data['order']) ? $data['order'] : '';

        $programs = auth()->user()->specialistIsActive()->first()->programs();

        if (!empty($data['name'])) {
            $programs->where('name', 'like', sprintf('%%%s%%', $data['name']));
        }

        if (array_key_exists('is_online', $data)) {
            $programs->where(['is_online' => $data['is_online']]);

            if (!$data['is_online']) {
                if (!empty($data['location_ids'])) {
                    $programs->whereIn('location_id', $data['location_ids']);
                } else {
                    $programs->whereNotNull('location_id');
                }
            }
        }

        if (!empty($data['is_free'])) {
            $programs->where(['price' => 0]);
        }

        $programs->with(['categories', 'programDates', 'programChapters', 'users', 'location']);

        if ($sort) {
            if ($sort == 'popular') {
                $programs->withCount(['users' => function($query) {
                    return $query->where('is_payed', '=', 1);
                }])->orderByDesc('users_count');
            } else {
                if ($order == 'desc') {
                    if ($sort == 'date') {
                        DB::select("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
                        $programDate = ProgramDate::query()->where('date', '>=', now())->orderByDesc('date')->orderByDesc('start_time')->groupBy('program_id');
                        $ids = $programDate->pluck('program_id')->toArray();
                        if (!empty($ids)) {
                            $ids_ordered = implode(',', $ids);
                            $programs->whereIn('programs.id', $ids);
                            $programs->orderByRaw("FIELD(programs.id, {$ids_ordered})");
                        }
                    } else {
                        $programs->orderByDesc($sort);
                    }
                } else {
                    if ($sort == 'date') {
                        DB::select("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
                        $programDate = ProgramDate::query()->where('date', '>=', now())->orderBy('date')->orderBy('start_time')->groupBy('program_id');
                        $ids = $programDate->pluck('program_id')->toArray();

                        if (!empty($ids)) {
                            $ids_ordered = implode(',', $ids);
                            $programs->whereIn('programs.id', $ids);
                            $programs->orderByRaw("FIELD(programs.id, {$ids_ordered})");
                        }
                    } else {
                        $programs->orderBy($sort);
                    }
                }
            }
        }

        return $this->sendResponse($programs->paginate(15));
    }

    /**
     * @return JsonResponse
     */
    public function myJoin(Request $request)
    {
        $data = $request->only(['name', 'sort', 'order', 'is_online', 'location_ids', 'category_ids', 'specialist_ids', 'start_date', 'end_date', 'is_free']);

        $validator = Validator::make($data, [
            'name' => 'nullable|string|max:100',
            'sort' => 'nullable|string|in:name,date,rate,price',
            'order' => 'nullable|string|in:asc,desc',
            // filters
            'is_online' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after:start_date',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'required|integer|exists:locations,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'required|integer|exists:program_categories,id',
            'specialist_ids' => 'nullable|array',
            'specialist_ids.*' => 'required|integer|exists:specialists,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sort = !empty($data['sort']) ? $data['sort'] : '';
        $order = !empty($data['order']) ? $data['order'] : '';

        $programs = auth()->user()->programs();

        if (!empty($data['name'])) {
            $programs->where('name', 'like', sprintf('%%%s%%', $data['name']));
        }

        if (array_key_exists('is_online', $data)) {
            $programs->where(['is_online' => $data['is_online']]);

            if (!$data['is_online']) {
                if (!empty($data['location_ids'])) {
                    $programs->whereIn('location_id', $data['location_ids']);
                } else {
                    $programs->whereNotNull('location_id');
                }
            }
        }

        if (!empty($data['is_free'])) {
            $programs->where(['price' => 0]);
        }

        if (!empty($data['category_ids'])) {
            $programs->whereHas('categories', function($query) use ($data) {
                $query->whereIn('program_category_id', $data['category_ids']);
            });
        }

        if (!empty($data['specialist_ids'])) {
            $programs->whereIn('specialist_id', $data['specialist_ids']);
        }

        $programs->with(['specialist.user', 'categories', 'programDates', 'programChapters', 'location']);

        if ($sort) {
            if ($sort == 'popular') {
                $programs->withCount(['users' => function($query) {
                    return $query->where('is_payed', '=', 1);
                }])->orderByDesc('users_count');
            } else {
                if ($order == 'desc') {
                    if ($sort == 'date') {
                        DB::select("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
                        $programDate = ProgramDate::query()->where('date', '>=', now())->orderByDesc('date')->orderByDesc('start_time')->groupBy('program_id');
                        $ids = $programDate->pluck('program_id')->toArray();
                        if (!empty($ids)) {
                            $ids_ordered = implode(',', $ids);
                            $programs->whereIn('programs.id', $ids);
                            $programs->orderByRaw("FIELD(programs.id, {$ids_ordered})");
                        }
                    } else {
                        $programs->orderByDesc($sort);
                    }
                } else {
                    if ($sort == 'date') {
                        DB::select("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
                        $programDate = ProgramDate::query()->where('date', '>=', now())->orderBy('date')->orderBy('start_time')->groupBy('program_id');
                        $ids = $programDate->pluck('program_id')->toArray();

                        if (!empty($ids)) {
                            $ids_ordered = implode(',', $ids);
                            $programs->whereIn('programs.id', $ids);
                            $programs->orderByRaw("FIELD(programs.id, {$ids_ordered})");
                        }
                    } else {
                        $programs->orderBy($sort);
                    }
                }
            }
        }

        return $this->sendResponse($programs->paginate(15));
    }

    /**
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only('presenter', 'presenter_id', 'category_ids', 'name', 'price', 'sale_price',
            'link', 'chapter_ids', 'location_id', 'is_online',
            'dates', 'description', 'gallery');

        $validator = Validator::make($data, [
            'presenter' => 'nullable|string|min:2|max:255',
            'presenter_id' => 'nullable|exists:users,id',
            'is_online' => 'required|boolean',
            'category_ids' => 'required|array|max:2',
            'category_ids.*' => 'required|integer|exists:program_categories,id',
            'name' => 'required|string|min:2|max:255|unique:programs',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'link' => 'nullable|string|max:255',
            'chapter_ids' => 'required|array|exists:chapters,id',
            'chapter_ids.*' => 'required|integer|exists:chapters,id',
            'location_id' => 'required_if:is_online,==,0|integer|exists:locations,id',
            'dates' => 'required_if:is_online,==,0|array',
            'dates.*.date' => 'required_if:is_online,==,0|date_format:Y-m-d',
            'dates.*.times' => 'nullable|array',
            'dates.*.times.*.start_time' => 'nullable|date_format:H:i',
            'dates.*.times.*.end_time' => 'nullable|date_format:H:i|after:dates.*.times.*.start_time',
            'description' => 'nullable|string|min:3|max:10000',

            'gallery' => 'required|array|max:10',
            'gallery.*' => 'required|mimes:jpg,jpeg,png|max:10240',
        ], [
            'dates.required_if' => 'Поле Дата обязательно для заполнения, если Вы создаете офлайн-программу.',
            'dates.*.date.required_if' => 'Поле Дата обязательно для заполнения, если Вы создаете офлайн-программу.',
            'dates.*.times.*.start_time' => [
                'date_format' => 'Поле :attribute не соответствует формату ЧЧ:мм.',
                'end_time' => 'Поле :attribute не соответствует формату ЧЧ:мм.',
            ],
            'location_id.required_if' => 'Поле местонахождение обязательно для заполнения, если Вы создаете офлайн-программу.',
            'category_ids.max' => ['array' => 'Можно выбрать только 2 категории.'],
        ], ['presenter_id' => 'Ведущий программы', 'category_ids' => 'Категория программы', 'category_ids.*' => 'категория программы', 'is_online' => 'Тип']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $user = auth()->user();

            /** @var Specialist $specialist */
            $specialist = $user->specialist()->first();

//            $group = Group::query()->create(['name' => substr(sprintf('Программа - %s', $data['name']), 0, 250), 'user_id' => $user->id]);
//            GroupMember::create(['group_id' => $group->id, 'user_id' => $user->id, 'is_admin' => 1]);

            $program = Program::query()->create([
                'presenter' => $data['presenter'] ?? null,
                'presenter_id' => $data['presenter_id'] ?? null,
                'specialist_id' => $specialist->id,
                'status' => Status::ACTIVE,
                //                'group_id' => $group->id,
                'name' => $data['name'],
                'is_online' => $data['is_online'],
                'location_id' => $data['location_id'] ?? null,
                'price' => empty($data['price']) ? 0 : $data['price'],
                'sale_price' => $data['sale_price'] ?? null,
                'link' => $data['link'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            foreach ($data['category_ids'] as $category_id) {
                ProgramProgramCategory::query()->create([
                    'program_id' => $program->id,
                    'program_category_id' => $category_id,
                ]);
            }

            foreach ($data['chapter_ids'] as $chapter_id) {
                ProgramChapter::query()->create([
                    'program_id' => $program->id,
                    'chapter_id' => $chapter_id,
                ]);
            }

            if (!empty($data['dates'])) {
                foreach ($data['dates'] as $dates) {
                    if (!empty($dates['times'])) {
                        foreach ($dates['times'] as $time) {
                            ProgramDate::query()->create([
                                'program_id' => $program->id,
                                'date' => !empty($dates['date']) ? $dates['date'] : null,
                                'start_time' => !empty($time['start_time']) ? $time['start_time'] : null,
                                'end_time' => !empty($time['end_time']) ? $time['end_time'] : null,
                            ]);
                        }
                    } elseif (!empty($dates['date'])) {
                        ProgramDate::query()->create([
                            'program_id' => $program->id,
                            'date' => $dates['date'],
                        ]);
                    }
                }
            }

            if (!empty($data['gallery'])) {
                $program->addMultipleMediaFromRequest(['gallery'])->each(function($fileAdder) {
                    $fileAdder->toMediaCollection('gallery');
                });
            }

            $firstImage = $program->gallery()->first();

            $program->update(['media_id' => $firstImage->id]);

            DB::commit();

            return $this->sendResponse(['message' => 'Программа успешно создана!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('presenter', 'presenter_id', 'category_ids', 'name', 'price', 'sale_price',
            'link', 'chapter_ids', 'location_id', 'is_online',
            'dates', 'description', 'gallery', 'removed_image_ids', 'removed_time_ids');

        $validator = Validator::make($data, [
            'presenter' => 'nullable|string|min:2|max:255',
            'presenter_id' => 'nullable|exists:users,id',
            'is_online' => 'required|boolean',
            'category_ids' => 'required|array|max:2',
            'category_ids.*' => 'required|integer|exists:program_categories,id',
            'name' => 'required|string|min:2|max:255|unique:programs,name,'.$id,
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'link' => 'nullable|string|max:255',
            'chapter_ids' => 'required|array|exists:chapters,id',
            'chapter_ids.*' => 'required|integer|exists:chapters,id',
            'location_id' => 'required_if:is_online,==,0|integer|exists:locations,id',
            'dates' => 'required_if:is_online,==,0|array',
            'dates.*.date' => 'required_if:is_online,==,0|date_format:Y-m-d',
            'dates.*.times' => 'nullable|array',
            'dates.*.times.*.start_time' => 'nullable|date_format:H:i',
            'dates.*.times.*.end_time' => 'nullable|date_format:H:i|after:dates.*.times.*.start_time',
            'description' => 'nullable|string|min:3|max:10000',

            'gallery' => 'nullable|array|max:10',
            'gallery.*' => 'required|mimes:jpg,jpeg,png|max:10240',
            'removed_time_ids' => 'nullable|array',
            'removed_time_ids.*' => 'required|integer|exists:program_dates,id',
            'removed_image_ids' => 'nullable|array',
            'removed_image_ids.*' => 'required|integer|exists:media,id',
        ], [
            'dates.required_if' => 'Поле Дата обязательно для заполнения, если Вы создаете офлайн-программу.',
            'dates.*.date.required_if' => 'Поле Дата обязательно для заполнения, если Вы создаете офлайн-программу.',
            'dates.*.times.*.start_time' => [
                'date_format' => 'Поле :attribute не соответствует формату ЧЧ:мм.',
                'end_time' => 'Поле :attribute не соответствует формату ЧЧ:мм.',
            ],
            'location_id.required_if' => 'Поле местонахождение обязательно для заполнения, если Вы создаете офлайн-программу.',
            'category_ids.max' => ['array' => 'Можно выбрать только 2 категории.'],
        ], ['presenter_id' => 'Ведущий программы', 'category_ids' => 'Категория программы', 'category_ids.*' => 'категория программы', 'is_online' => 'Тип']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $user = auth()->user();

            /** @var Specialist $specialist */
            $specialist = $user->specialist()->first();

            /** @var Program $program */
            $program = $specialist->programs()->where(['id' => $id])->first();

            if (empty($program)) {
                return $this->sendError();
            }

//            if ($data['name'] != $program->name) {
//                Group::query()->where(['id' => $program->group_id])->update([
//                    'name' => substr(sprintf('Программа - %s', $data['name']), 0, 250)
//                ]);
//            }

            $program->update([
                'presenter' => $data['presenter'] ?? null,
                'presenter_id' => $data['presenter_id'] ?? null,
                'name' => $data['name'],
                'location_id' => $data['location_id'] ?? null,
                'is_online' => $data['is_online'],
                'price' => empty($data['price']) ? 0 : $data['price'],
                'sale_price' => $data['sale_price'] ?? null,
                'link' => $data['link'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            $program->categories()->sync($data['category_ids']);

            $program->programChapters()->sync($data['chapter_ids']);

            if (!empty($data['removed_time_ids'])) {
                $program->programDates()->whereIn('id', $data['removed_time_ids'])->delete();
            }

            if (!empty($data['dates'])) {
                foreach ($data['dates'] as $dates) {
                    if (!empty($dates['times'])) {
                        foreach ($dates['times'] as $time) {
                            $program->programDates()->updateOrCreate([
                                'date' => $dates['date'] ?? null,
                                'start_time' => $time['start_time'] ?? null,
                                'end_time' => $time['end_time'] ?? null,
                            ]);
                        }
                    } elseif (!empty($dates['date'])) {
                        $program->programDates()->updateOrCreate([
                            'date' => $dates['date'],
                            'start_time' => null,
                            'end_time' => null,
                        ]);
                    }
                }
            }

            $gallery = $program->gallery();

            if (!empty($data['removed_image_ids'])) {
                if ($gallery->count() == count($data['removed_image_ids']) && empty($data['gallery'])) {
                    return $this->sendError(['message' => 'Вы не можете удалить все фотографии'], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                foreach ($data['removed_image_ids'] as $img_id) {
                    $program->deleteMedia($img_id);
                }
            }

            if (!empty($data['gallery'])) {
                $program->addMultipleMediaFromRequest(['gallery'])->each(function($fileAdder) {
                    $fileAdder->toMediaCollection('gallery');
                });
            }

            $firstImage = $gallery->first();

            $program->update(['media_id' => $firstImage->id, 'status' => Status::FOR_CHECKING]);

            DB::commit();

            return $this->sendResponse(['message' => 'Данные успешно обновлены и отправлены на проверку']);
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        $program = Program::query()->where('id', $id);

        if ($program->exists()) {
            return $this->sendResponse($program->with(['specialist.user', 'specialist.specialties', 'categories', 'programDates', 'programChapters', 'users', 'location'])->first());
        }

        return $this->sendError();
    }

    /**
     * @return JsonResponse
     */
    public function category()
    {
        return $this->sendResponse(ProgramCategory::all());
    }

    /**
     * @return JsonResponse
     */
    public function chapter(Request $request)
    {
        $chapters = Chapter::query()->whereNull('type');
        $type = $request->get('type');

        if (in_array($type, [Chapter::ONLINE, Chapter::OFFLINE])) {
            $chapters->orWhere(['type' => $type]);
        }

        return $this->sendResponse($chapters->get());
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function user($id)
    {
        $program_users = ProgramUser::query()->where(['program_id' => $id, 'is_payed' => 1])->get()->pluck('user_id');

        $users = User::query()->with('programUser')->where(['status_id' => UserStatus::ACTIVE])->whereIn('id', $program_users);

        return $this->sendResponse($users->paginate(15));
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function join($id)
    {
        $program = Program::query()->with(['specialist'])->where(['id' => $id, 'status' => Status::ACTIVE])->first();

        if (empty($program)) {
            return $this->sendError();
        }

//        if ($program->users()->count() == $program->first()->member_count) {
//            return $this->sendError(['message' => 'Больше нет мест'], Response::HTTP_FORBIDDEN);
//        }

        $auth = auth()->user();
        $user_id = $auth->id;

        if ($program->specialist->user_id == $user_id) {
            return $this->sendError(['message' => 'Вы не можете присоединится к программу'], Response::HTTP_FORBIDDEN);
        }

        if (!ProgramUser::query()->where(['user_id' => $user_id, 'program_id' => $id])->exists()) {
            $auth->programs()->attach($id);

            return $this->sendResponse(['message' => 'Успешно присоединился к программе']);
        }

        return $this->sendError('', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param mixed $program_id
     *
     * @return JsonResponse
     */
    public function addUsers(Request $request, $program_id)
    {
        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        $program = Program::query()->with(['specialist'])->where(['id' => $program_id, 'specialist_id' => $specialist_id, 'status' => Status::ACTIVE])->first();

        if (empty($program)) {
            return $this->sendError();
        }

        $data = $request->only('user_ids');

        $validator = Validator::make($data, [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'distinct', Rule::exists('users', 'id')->whereNot('id', $auth->id)],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();

//        if ($program->users()->count() == $program->first()->member_count) {
//            return $this->sendError(['message' => 'Больше нет мест'], Response::HTTP_FORBIDDEN);
//        }

        $program->users()->syncWithPivotValues($validated['user_ids'], ['is_specialist_seen' => true, 'is_payed' => true]);

        return $this->sendResponse(['message' => 'Успешно присоединились к программе']);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function book($id)
    {
        $program = Program::query()->with(['specialist'])->where(['id' => $id, 'status' => Status::ACTIVE])->first();

        if (empty($program)) {
            return $this->sendError();
        }

        $auth = auth()->user();
        $user_id = $auth->id;

        if ($program->specialist->user_id == $user_id) {
            return $this->sendError(['message' => 'Вы не можете забронировать эту программу'], Response::HTTP_FORBIDDEN);
        }

        $userProgram = ProgramUser::query()->where(['user_id' => $user_id, 'program_id' => $id, 'is_payed' => 0]);

        if ($userProgram->exists()) {
            DB::beginTransaction();

            try {
                $userProgram->first()->update(['is_payed' => 1, 'is_seen' => 0]);
                $specialist_user_id = $program->specialist->user->id;

//                if (empty($program->group_id)) {
//                    $group = Group::query()->create(['name' => substr(sprintf('Программа - %s', $program->name), 0, 250), 'user_id' => $specialist_user_id]);
//                    GroupMember::create(['group_id' => $group->id, 'user_id' => $specialist_user_id, 'is_admin' => 1]);
//                    GroupMember::create(['group_id' => $group->id, 'user_id' => $user_id]);
//                    $program->update(['group_id' => $group->id]);
//                } else {
//                    GroupMember::create(['group_id' => $program->group_id, 'user_id' => $user_id]);
//                }

                $device_tokens = UserDevice::query()->where('user_id', $specialist_user_id)->pluck('token')->toArray();

                Notification::sendNotification($device_tokens, $auth->full_name, sprintf('Забронировал%s программу - %s', !$auth->gender ? '(а)' : ($auth->gender == 'female' ? 'а' : ''), $program->name));

                DB::commit();

                return $this->sendResponse(['message' => 'Успешно забронировано']);
            } catch (\Exception $e) {
                DB::rollBack();
            }

            return $this->sendInternalError();
        }

        return $this->sendError('', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @return JsonResponse
     */
    public function see()
    {
        $auth = auth()->user();
        $user_id = $auth->id;

        $userProgram = ProgramUser::query()->where(['user_id' => $user_id, 'is_payed' => 1, 'is_seen' => 0]);

        if ($userProgram->exists()) {
            try {
                $userProgram->update(['is_seen' => 1]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());

                return $this->sendInternalError();
            }
        }

        return $this->sendResponse('', Response::HTTP_ACCEPTED);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function specialistSee($id)
    {
        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        $program = Program::query()->where(['id' => $id, 'specialist_id' => $specialist_id])->first();

        if (empty($program)) {
            return $this->sendError();
        }

        $userProgram = ProgramUser::query()->where(['program_id' => $program->id, 'is_payed' => 1, 'is_specialist_seen' => 0]);

        if ($userProgram->exists()) {
            try {
                $userProgram->update(['is_specialist_seen' => 1]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());

                return $this->sendInternalError();
            }
        }

        return $this->sendResponse('', Response::HTTP_ACCEPTED);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function leave($id)
    {
        $program = Program::query()->find($id);

        if (empty($program)) {
            return $this->sendError();
        }

        $auth = auth()->user();

        if (ProgramUser::query()->where(['user_id' => $auth->id, 'program_id' => $id])->exists()) {
            DB::beginTransaction();

            try {
                $auth->programs()->detach($id);
                $group_id = $program->group_id;
                $specialist_user_id = $program->specialist->user->id;

                if ($group_id) {
                    $member_in_group = GroupMember::query()->where(['group_id' => $group_id, 'user_id' => $auth->id, 'is_deleted' => 0, 'is_admin' => 0]);
                    if ($member_in_group->exists()) {
                        $member_in_group->update(['is_deleted' => 1]);
                    }

                    $groupMessage = GroupMessage::query()->where(['user_id' => $auth, 'group_id' => $group_id]);

                    if ($groupMessage->exists()) {
                        $groupMessage->update(['is_deleted' => 1]);
                    }

                    if ($first = $groupMessage->first()) {
                        $first->groupMessageEvent()->update(['is_deleted' => 1]);
                    }
                }

                $device_tokens = UserDevice::query()->where('user_id', $specialist_user_id)->pluck('token')->toArray();

                Notification::sendNotification($device_tokens, $auth->full_name, sprintf('Покинул%s программу - %s', $auth->gender == 'female' ? 'а' : '', $program->name));

                DB::commit();

                return $this->sendResponse(['message' => 'Успешно покинули программу']);
            } catch (\Exception $e) {
                DB::rollBack();
            }

            return $this->sendInternalError();
        }

        return $this->sendError('', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function activate($id)
    {
        $auth = auth()->user();

        $specialist = $auth->specialist()->first();

        $program = Program::query()->where(['id' => $id, 'specialist_id' => $specialist->id]);

        if (!$program->exists()) {
            return $this->sendError();
        }

        try {
            $single = $program->first();

            if ($single->status == Status::INACTIVE) {
                $single->update(['status' => Status::ACTIVE]);

                return $this->sendResponse(['message' => 'Программа видна всем и отображается на странице "Все программы"']);
            }

            return $this->sendResponse(['message' => 'Программа находится на проверке']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function inactivate($id)
    {
        $auth = auth()->user();

        $specialist = $auth->specialist()->first();

        $program = Program::query()->where(['id' => $id, 'specialist_id' => $specialist->id]);

        if (!$program->exists()) {
            return $this->sendError();
        }

        try {
            $single = $program->first();

            if ($single->status == Status::ACTIVE) {
                $single->update(['status' => Status::INACTIVE]);

                return $this->sendResponse(['message' => 'Программа скрыта и видна только Вам']);
            }

            return $this->sendResponse(['message' => 'Программа находится на проверке']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = auth()->user();

        $program = $user->specialistIsActive()->first()->programs()->where('id', $id)->first();

        if (!empty($program)) {
            $program->clearMediaCollection('gallery')->delete();
        }

        return $this->sendResponse(['message' => 'Программа удалена'], Response::HTTP_ACCEPTED);
    }
}
