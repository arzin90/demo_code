<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Constants\Status;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\GroupMessageEvent;
use App\Models\MutedGroup;
use App\Models\Program;
use App\Models\ProgramUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        return $this->sendResponse(auth()->user()->groupMembares()->whereHas('group', function($query) {
            return $query->where(['status' => Status::ACTIVE]);
        })->with('group')->where(['status' => Status::ACTIVE, 'is_deleted' => 0])
            ->paginate(ParamConstant::GROUP_COUNT));
    }

    /**
     * @return JsonResponse
     */
    public function my()
    {
        return $this->sendResponse(auth()->user()->groups()->where(['status' => Status::ACTIVE])->paginate(ParamConstant::GROUP_COUNT));
    }

    /**
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only('name', 'program_id', 'image');

        $specialist_id = auth()->user()->specialist()->first()->id;

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:5120',
            'program_id' => [
                'nullable',
                Rule::exists('programs', 'id')->where(function($query) use ($specialist_id) {
                    return $query->where('specialist_id', $specialist_id);
                }),
            ],
        ], [], ['program_id' => 'Програм']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $user_id = auth()->id();

            /** @var Group $group */
            $group = Group::query()->create(['name' => $data['name'], 'user_id' => $user_id]);
            GroupMember::create(['group_id' => $group->id, 'user_id' => $user_id, 'is_admin' => 1]);

            if (!empty($data['program_id'])) {
                if (!Program::query()->where(['id' => $data['program_id'], 'specialist_id' => $specialist_id])->has('group')->exists()) {
                    Program::query()->find($data['program_id'])->update(['group_id' => $group->id]);
                }
            }

            if ($request->has('image') && $request->file('image')) {
                $group->addMedia($request->file('image'))
                    ->toMediaCollection('image');
            }

            DB::commit();

            return $this->sendResponse(Group::find($group->id), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function update($id, Request $request)
    {
        $data = $request->only('name', 'image');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user_id = auth()->id();

            /** @var Group $group */
            $group = Group::query()->where(['id' => $id, 'user_id' => $user_id])->first();

            if (empty($group)) {
                return $this->sendError();
            }

            $group->update(['name' => $data['name']]);

            if ($request->has('image') && $request->file('image')) {
                $group->clearMediaCollection('image');
                $group->addMedia($request->file('image'))
                    ->toMediaCollection('image');
            }

            $updated_group = $group->refresh();

            return $this->sendResponse([
                'name' => $updated_group->name,
                'image_url' => $updated_group->image_url,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function show($id)
    {
        $auth_id = auth()->id();
        $is_on_group = GroupMember::query()->where(['group_id' => $id, 'user_id' => $auth_id, 'status' => Status::ACTIVE, 'is_deleted' => 0])->exists();

        $group = Group::query()->where(['id' => $id, 'status' => Status::ACTIVE]);

        if (!$is_on_group || !$group->exists()) {
            return $this->sendError();
        }

        return $this->sendResponse($group->with(['members.user'])->first());
    }

    /**
     * @return JsonResponse
     */
    public function addMember(Request $request, $id)
    {
        $auth_id = auth()->id();
        $is_admin = GroupMember::query()->where(['group_id' => $id, 'user_id' => $auth_id, 'is_admin' => 1])->exists();

        if (!$is_admin) {
            return $this->sendError();
        }

        $members = $request->only('members');

        $validator = Validator::make($members, [
            'members' => 'required|array|min:1|max:50',
            'members.*' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $program_group = Program::query()->where(['group_id' => $id, 'specialist_id' => auth()->user()->specialist()->first()->id])->first();

        try {
            DB::beginTransaction();

            foreach (array_unique($members['members']) as $user_id) {
                if (!empty($program_group)) {
                    $program_users = ProgramUser::query()->where(['program_id' => $program_group->id, 'user_id' => $user_id, 'is_payed' => 1])->exists();

                    if (!$program_users) {
                        DB::rollBack();

                        return $this->sendError(['message' => 'Недопустимые участники для этой группы'], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }

                GroupMember::query()->firstOrCreate(['group_id' => $id, 'user_id' => $user_id])->update(['status' => Status::ACTIVE, 'is_deleted' => 0]);
            }

            DB::commit();

            return $this->sendResponse(['message' => 'Добавлено']);
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function showMembers($id)
    {
        $member_in_group = auth()->user()->groupMembares()->with('user')->where(['group_id' => $id, 'status' => Status::ACTIVE, 'is_deleted' => 0]);

        if (!$member_in_group->exists()) {
            return $this->sendError();
        }

        $data = GroupMember::query()->with('user')->where(['group_id' => $id, 'status' => Status::ACTIVE, 'is_deleted' => 0])
            ->paginate(ParamConstant::MEMBER_COUNT);

        return $this->sendResponse(['group' => Group::find($id), 'members_count' => $data->count(), 'members' => $data]);
    }

    /**
     * @return JsonResponse
     */
    public function leaveMember($id)
    {
        $auth_id = auth()->id();
        $member_in_group = GroupMember::query()->where(['group_id' => $id, 'user_id' => $auth_id, 'status' => Status::ACTIVE, 'is_deleted' => 0, 'is_admin' => 0])->first();

        if (empty($member_in_group)) {
            return $this->sendError();
        }

        try {
            $member_in_group->update(['is_deleted' => 1]);
            $groupMessage = GroupMessage::query()->where(['user_id' => auth()->id(), 'group_id' => $id]);

            $message = $groupMessage->first();

            if (!empty($message)) {
                $message->update(['is_deleted' => 1]);
                $message->groupMessageEvent()->update(['status' => Status::DELETED, 'is_deleted' => 1]);
            }

            return $this->sendResponse(['message' => 'Вы вышли из группы.'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function mute($id)
    {
        $validator = Validator::make(['group' => $id], [
            'group' => 'required|exists:groups,id',
        ], [], ['group' => 'группа']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $muted = MutedGroup::query()->updateOrCreate(['user_id' => auth()->id(), 'group_id' => $id]);

            if ($muted->wasRecentlyCreated) {
                return $this->sendResponse(['message' => 'Уведомления отключены']);
            }

            return $this->sendResponse(['message' => 'Уже отключен']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function unmute($id)
    {
        $validator = Validator::make(['group' => $id], [
            'group' => 'required|exists:groups,id',
        ], [], ['group' => 'группа']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $muted = MutedGroup::query()->where(['user_id' => auth()->id(), 'group_id' => $id]);

        try {
            if ($muted->exists()) {
                $muted->delete();

                return $this->sendResponse(['message' => 'Уведомления включены']);
            }

            return $this->sendResponse(['message' => 'Уже включен']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function removeMember($id, $member_id)
    {
        $auth_id = auth()->id();

        if ($auth_id == $member_id) {
            return $this->sendError(['message' => 'Вы не можете удалить себя из чата'], Response::HTTP_FORBIDDEN);
        }

        $is_admin = GroupMember::query()->where(['group_id' => $id, 'user_id' => $auth_id, 'is_admin' => 1])->exists();

        if (!$is_admin) {
            return $this->sendError();
        }

        $member_in_group = GroupMember::query()->where(['group_id' => $id, 'status' => Status::ACTIVE, 'user_id' => $member_id, 'is_deleted' => 0])->first();

        if (empty($member_in_group)) {
            return $this->sendError();
        }

        try {
            $member_in_group->update(['status' => Status::DELETED, 'is_deleted' => 1]);

            return $this->sendResponse(['message' => 'Участник удален'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $group = auth()->user()->groups()->where(['id' => $id, 'status' => Status::ACTIVE])->first();

        if (!empty($group)) {
            $group->update(['status' => Status::DELETED]);

            return $this->sendResponse(['message' => 'Группа удалена'], Response::HTTP_ACCEPTED);
        }

        $auth_id = auth()->id();

        $member_in_group = GroupMember::query()->where(['group_id' => $id, 'user_id' => $auth_id, 'is_deleted' => 0])->first();

        if (!empty($member_in_group)) {
            GroupMessage::query()->where(['group_id' => $id, 'user_id' => $auth_id, 'is_deleted' => 0])->update(['is_deleted' => 1]);

            $group_message_ids = GroupMessage::query()->where(['group_id' => $id])
                ->where('user_id', '<>', $auth_id)->pluck('id');

            if (!empty($group_message_ids)) {
                GroupMessageEvent::query()->where(['user_id' => $auth_id, 'is_deleted' => 0])
                    ->whereIn('group_message_id', $group_message_ids)->update(['is_deleted' => 1]);
            }
        }

        return $this->sendResponse(['message' => 'Группа удалена'], Response::HTTP_ACCEPTED);
    }
}
