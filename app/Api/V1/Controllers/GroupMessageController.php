<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Constants\Status;
use App\Events\MessageCountEvent;
use App\Events\MessageGroupEvent;
use App\Events\RequestGroupMessageEvent;
use App\Helper\Notification;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\GroupMessageEvent;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GroupMessageController extends BaseController
{
    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function index($id, Request $request)
    {
        $auth = auth()->user();
        $auth_id = $auth->id;
        $s = $request->get('s', '') ?? '';

        $is_in_active_group = GroupMember::query()->where([
            'status' => Status::ACTIVE,
            'group_id' => $id,
            'user_id' => $auth_id,
            'is_deleted' => 0])->exists();

        if (!$is_in_active_group) {
            return $this->sendError();
        }

        $deleted_message_ids = $auth->groupMessageEvents()->where(['is_deleted' => 1])->pluck('group_message_id');

        if ($deleted_message_ids->isEmpty()) {
            $messages = GroupMessage::query()->where(['group_id' => $id])->with(['user']);
        } else {
            $messages = GroupMessage::query()->where(['group_id' => $id])->with(['user'])->whereNotIn('id', $deleted_message_ids);
        }

        if (Str::length($s) > 0) {
            $search = sprintf('%%%s%%', $s);
            $messages->where('message', 'like', $search);
        }

        $get_messages = clone $messages->whereNotExists(function($query) {
            return $query->where(['is_deleted' => 1, 'user_id' => auth()->id()]);
        });

        if ($get_messages->exists()) {
            $auth->groupMessageEvents()->where(['is_deleted' => 0, 'is_read' => 0])->whereIn('group_message_id', $get_messages->pluck('id'))->update(['is_read' => 1]);
        }

        return $this->sendResponse($messages->latest()->paginate(ParamConstant::MESSAGE_COUNT));
    }

    /**
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only('message', 'file');
        $file = $request->file('file');
        $group_id = $request->route('id');

        $validator = Validator::make($request->all(), [
            'message' => 'nullable|max:1000',
            'file' => 'nullable|mimes:jpg,bmp,png,jpeg,gif,pdf,doc,docx,xlsx|max:10240',
        ]);

        $validator->after(function($validator) use ($group_id) {
            if (!Group::query()->where(['id' => $group_id, 'status' => Status::ACTIVE])->exists()
                || !GroupMember::query()->where(['group_id' => $group_id, 'status' => Status::ACTIVE, 'is_deleted' => 0])->exists()) {
                $validator->errors()->add(
                    'group_id', 'Неверный идентификатор пользователя'
                );
            }
        });

        if ($validator->fails() || empty($file) && empty($data['message'])) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $auth = auth();
            $auth_user = $auth->user();
            $auth_id = $auth->id();
            GroupMember::query()->where(['group_id' => $group_id, 'status' => Status::ACTIVE, 'is_deleted' => 1])->update(['is_deleted' => 0]);

            $group_members = GroupMember::query()->whereHas('user')->where(['group_id' => $group_id, 'status' => Status::ACTIVE, 'is_deleted' => 0])->where('user_id', '<>', $auth_id)->pluck('user_id')->toArray();
            $is_file = false;

            if (!empty($data['message'])) {
                $message_new = GroupMessage::query()->with('group')->create(['user_id' => $auth_id, 'group_id' => $group_id, 'message' => $data['message']]);
                $message = $message_new->refresh();
            } elseif (!empty($file)) {
                $message_new = GroupMessage::query()->with('group')->create(['user_id' => $auth_id, 'group_id' => $group_id]);
                $message_new->addMedia($request->file('file'))
                    ->toMediaCollection('file');
                $message = $message_new->refresh();
                $is_file = true;
            }

            if (!empty($group_members)) {
                $device_tokens = [];
                $params = [
                    'id' => $auth_id,
                    'first_name' => $auth_user->first_name,
                    'last_name' => $auth_user->last_name,
                    'avatar_url' => $auth_user->avatar_url,
                    'phone' => $auth_user->email,
                    'email' => $auth_user->phone,
                ];

                event(new MessageGroupEvent($message, $params));

                foreach ($group_members as $group_member) {
                    GroupMessageEvent::query()->create(['user_id' => $group_member,
                        'group_message_id' => $message_new->id]);
                    $to_unread_count_all = User::query()->find($group_member)->unread_count_all;

                    event(new RequestGroupMessageEvent($group_member, $message));
                    event(new MessageCountEvent($to_unread_count_all, $group_member));

                    $device_tokens = array_merge($device_tokens, UserDevice::query()
                        ->where('user_id', $group_member)->pluck('token')->toArray());
                }

                if ($is_file) {
                    $_file = $message->toArray()['file'];
                    $FCM_res = Notification::sendNotification($device_tokens, $auth_user->full_name, $_file['url'], [
                        'form' => $params, 'group_id' => $group_id, 'group_name' => $message_new->group->name, 'message_id' => $message_new->id], true);
                } else {
                    $FCM_res = Notification::sendNotification($device_tokens, $auth_user->full_name, $data['message'], [
                        'form' => $params, 'group_id' => $group_id, 'group_name' => $message_new->group->name, 'message_id' => $message_new->id]);
                }
            }

            return $this->sendResponse(['message' => $message]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendError(['message' => 'Не найден']);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var GroupMessage $sender_message */
        $sender_message = $user->groupMessages()->where(['id' => $id])->first();

        /** @var GroupMessageEvent $recipient_message */
        $recipient_message = $user->groupMessageEvents()->where(['group_message_id' => $id])->first();

        if (!empty($sender_message)) {
            $sender_message->update(['is_deleted' => 1]);

            if ($request->get('recipient') == 1) {
                GroupMessageEvent::query()->where(['group_message_id' => $id])->update(['is_deleted' => 1]);
            }
        } elseif (!empty($recipient_message)) {
            $recipient_message->update(['is_deleted' => 1]);
        }

        return $this->sendResponse(['message' => 'Сообщение удалено'], Response::HTTP_ACCEPTED);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function destroyAll($id, Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $messages = $request->only(['messages', 'recipient']);

        try {
            if (!empty($messages['messages'])) {
                $validator = Validator::make($messages, [
                    'messages' => 'required|array',
                    'messages.*' => 'integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $message_ids = $messages['messages'];

                $user->groupMessages()->whereIn('id', $message_ids)
                    ->where(['group_id' => $id, 'is_deleted' => 0])->update(['is_deleted' => 1]);

                if ($request->get('recipient') == 1) {
                    foreach ($message_ids as $message_id) {
                        if ($user->groupMessages()->where('id', $message_id)->exists()) {
                            GroupMessageEvent::query()->where('group_message_id', $message_id)->update(['is_deleted' => 1]);
                        }
                    }
                }

                $user->groupMessageEvents()->whereIn('group_message_id', $message_ids)
                    ->where(['is_deleted' => 0])->update(['is_deleted' => 1]);

                $text = 'Сообщения удалены';
            } else {
                /* @var $sender_message GroupMessage */
                $user->groupMessages()->where(['group_id' => $id, 'is_deleted' => 0])->update(['is_deleted' => 1]);

                $group_message_ids = GroupMessage::query()->where(['group_id' => $id])
                    ->where('user_id', '<>', $user->id)->pluck('id');

                if (!empty($group_message_ids)) {
                    $user->groupMessageEvents()->where(['is_deleted' => 0])->whereIn('group_message_id', $group_message_ids)->update(['is_deleted' => 1]);
                }

                $text = 'Чат удален';
            }

            return $this->sendResponse(['message' => $text], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }
}
