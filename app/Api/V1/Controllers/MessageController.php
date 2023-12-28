<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Constants\Status;
use App\Events\MessageCountEvent;
use App\Events\MessageEvent;
use App\Events\MessageRemoveEvent;
use App\Events\RequestEvent;
use App\Helper\Notification;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\GroupMessageEvent;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MessageController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function users()
    {
        $auth_id = auth()->id();

        $from_message = Message::query()->where(['to' => $auth_id, 'to_deleted' => 0])->groupBy('from')->pluck('from')->toArray();
        $to_message = Message::query()->where(['from' => $auth_id, 'from_deleted' => 0])->groupBy('to')->pluck('to')->toArray();

        $users = User::query()->whereIn('id', array_unique(array_merge($from_message, $to_message)))
            ->select(['id', 'status_id', 'first_name', 'last_name', 'patronymic_name', 'email', 'phone'])->get();

        $group_ids = auth()->user()->groupMembares()->whereHas('group', function($query) {
            return $query->where(['status' => Status::ACTIVE]);
        })->where(['is_deleted' => 0])->pluck('group_id');
        $groups = Group::query()->whereIn('id', $group_ids)->get();

        $current_page = request()->get('page') ?? 1;
        $merged_list = array_merge($users->toArray(), $groups->toArray());

        if (!empty($merged_list)) {
            $sorted_list = collect($merged_list)
                ->sortByDesc(function($product, $key) {
                    return is_null($product['last_message']) ? $product['created_at'] : $product['last_message']['created_at'];
                })->values()->forPage($current_page, ParamConstant::MESSAGE_USER_COUNT);

            $userList = new LengthAwarePaginator(
                $sorted_list,
                count($merged_list),
                ParamConstant::MESSAGE_USER_COUNT,
                $current_page, [
                    'path' => URL::current(),
                    'query' => [
                        'page' => $current_page,
                    ],
                ]
            );

            $sent_message_group_count = GroupMessage::query()
                ->where(['user_id' => $auth_id, 'is_deleted' => 0])
                ->count();

            $received_message_group_count = GroupMessageEvent::query()
                ->leftJoin('group_messages', 'group_message_id', '=', 'group_messages.id')
                ->where(['group_message_events.is_deleted' => 0])
                ->where('group_messages.user_id', '<>', $auth_id)
                ->where(['group_message_events.user_id' => $auth_id])
                ->count();

            return $this->sendResponse([
                'message_count' => Message::query()->where(['from' => $auth_id, 'from_deleted' => 0])
                    ->orWhere(function($query) use ($auth_id) {
                        return $query->where(['to' => $auth_id, 'to_deleted' => 0]);
                    })->count() + $sent_message_group_count + $received_message_group_count,
                'chat' => $userList,
            ]);
        }

        return $this->sendResponse([]);
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function index(Request $request, $id)
    {
        $auth_id = auth()->id();
        $s = $request->get('s', '') ?? '';

        $messages = Message::query()->where(['from' => $id, 'to' => $auth_id, 'to_deleted' => 0]);
        $update = clone $messages;

        if ($s) {
            $search = sprintf('%%%s%%', $s);
            $messages->where('message', 'like', $search);
        }

        $data = $messages->with(['from', 'to'])->orWhere(function($query) use ($id, $auth_id, $s) {
            $query->where(['from' => $auth_id, 'to' => $id, 'from_deleted' => 0]);
            if ($s) {
                $search = sprintf('%%%s%%', $s);
                $query->where('message', 'like', $search);
            }
        })->latest()->paginate(ParamConstant::MESSAGE_COUNT);

        $update->where(['to_read' => 0])->update(['to_read' => 1]);

//        event(new MessageCountEvent(auth()->user()->unread_count_all, $auth_id));

        return $this->sendResponse($data);
    }

    /**
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        /** @var User $auth */
        $auth = auth()->user();

        $s = $request->get('s', '') ?? '';

        if (Str::length($s) > 0) {
            $auth_id = $auth->id;
            $search = sprintf('%%%s%%', $s);

            $from_message = Message::query()->where(['to' => $auth_id, 'to_deleted' => 0])->groupBy('from')->pluck('from')->toArray();
            $to_message = Message::query()->where(['from' => $auth_id, 'from_deleted' => 0])->groupBy('to')->pluck('to')->toArray();

            $itemsUser = $itemsMy = $itemsOther = [];
            $type = $request->get('type');

            if (!empty($from_message) || !empty($to_message)) {
                $users = User::query()->whereIn('id', array_unique(array_merge($from_message, $to_message)))
                    ->where(function($query) use ($search) {
                        return $query->orWhere('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('patronymic_name', 'like', $search);
                    })
                    ->select(['id', 'first_name', 'last_name', 'patronymic_name', 'email', 'phone'])->paginate(ParamConstant::MESSAGE_USER_COUNT);

                $itemsUser = new LengthAwarePaginator(
                    $users,
                    $users->total(),
                    $users->perPage(),
                    $users->currentPage(), [
                        'path' => sprintf('%s?type=user', URL::current()),
                        'query' => [
                            'page' => $users->currentPage(),
                        ],
                    ]
                );

                if ($type == 'user') {
                    return $this->sendResponse($itemsUser);
                }
            }

            if ($type != 'group_message') {
                $message = Message::query()->with(['from', 'to'])->where(['from' => $auth_id, 'from_deleted' => 0])
                    ->where('message', 'like', $search)->orWhere(function(Builder $query) use ($auth_id, $search) {
                        return $query->where(['to' => $auth_id, 'to_deleted' => 0])->where('message', 'like', $search);
                    })->whereNotNull('from')->whereNotNull('to')->paginate(ParamConstant::MESSAGE_COUNT);

                $other = collect($message->toArray()['data'])->groupBy(function($item, $key) use ($auth_id) {
                    if (!empty($item['from'])) {
                        if ($auth_id == $item['from']['id']) {
                            if (!empty($item['to'])) {
                                return sprintf('#%d %s %s', $item['to']['id'], $item['to']['first_name'], $item['to']['last_name']);
                            }

                            return sprintf('#%d deleted user', $auth_id);
                        }

                        return sprintf('#%d %s %s', $item['from']['id'], $item['from']['first_name'], $item['from']['last_name']);
                    }

                    return sprintf('#%d deleted user', $auth_id);
                })->all();

                $messages = new LengthAwarePaginator(
                    $other,
                    $message->total(),
                    $message->perPage(),
                    $message->currentPage(), [
                        'path' => sprintf('%s?type=message', URL::current()),
                        'query' => [
                            'page' => $message->currentPage(),
                        ],
                    ]
                );

                if ($type == 'message') {
                    return $this->sendResponse($messages);
                }
            }

            $deletedGroupIds = GroupMember::query()->select(['group_id'])->where(['user_id' => $auth_id, 'is_deleted' => 1])
                ->orWhere(function($query) use ($auth_id) {
                    return $query->where(['user_id' => $auth_id])->where('status', '<>', Status::ACTIVE);
                })->get()->pluck('group_id')->toArray();

            $messageEventsGroupMessageIds = GroupMessageEvent::query()->join('group_messages', 'group_messages.id', '=', 'group_message_events.group_message_id')
                ->where(['group_message_events.user_id' => $auth_id, 'group_message_events.is_deleted' => 0])
                ->whereNotIn('group_messages.user_id', $deletedGroupIds)
                ->get()->pluck('group_message_id')->toArray();

            $groupMessage = $auth->groupMessages()->with(['group', 'user'])
                ->whereNotIn('group_id', $deletedGroupIds)
                ->where('is_deleted', 0)
                ->orWhereIn('group_messages.id', $messageEventsGroupMessageIds)
                ->having('message', 'like', $search)->paginate(ParamConstant::MESSAGE_COUNT);

            $otherGroup = collect($groupMessage->toArray()['data'])->groupBy(function($item, $key) use ($auth_id) {
                return sprintf('#%d %s', $item['group']['id'], $item['group']['name']);
            })->transform(function($item, $k) {
                return $item->groupBy(function($item, $key) {
                    return sprintf('#%d %s %s', $item['user']['id'], $item['user']['first_name'], $item['user']['last_name']);
                });
            })->all();

            $groupMessages = new LengthAwarePaginator(
                $otherGroup,
                $groupMessage->total(),
                $groupMessage->perPage(),
                $groupMessage->currentPage(), [
                    'path' => sprintf('%s?type=group_message', URL::current()),
                    'query' => [
                        'page' => $groupMessage->currentPage(),
                    ],
                ]
            );

            if ($type == 'group_message') {
                return $this->sendResponse($groupMessages);
            }

            return $this->sendResponse([
                'users' => $itemsUser,
                'messages' => $messages,
                'groupMessages' => $groupMessages,
            ]);
        }

        return $this->sendResponse([]);
    }

    /**
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only('message', 'file');
        $file = $request->file('file');
        $to = $request->route('id');

        $validator = Validator::make($request->all(), [
            'message' => 'nullable|max:1000',
            'file' => 'nullable|mimes:jpg,bmp,png,jpeg,gif,pdf,doc,docx,xlsx|max:10240',
        ]);

        $validator->after(function($validator) use ($to) {
            if ($to == auth()->id() || !User::query()->where(['id' => $to, 'status_id' => UserStatus::ACTIVE])->exists()) {
                $validator->errors()->add(
                    'user_id', 'Неверный идентификатор пользователя'
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
            $device_tokens = UserDevice::query()->where('user_id', $to)->pluck('token')->toArray();
            $params = [
                'id' => $auth_id,
                'first_name' => $auth_user->first_name,
                'last_name' => $auth_user->last_name,
                'avatar_url' => $auth_user->avatar_url,
                'phone' => $auth_user->email,
                'email' => $auth_user->phone,
            ];

            $to_unread_count_all = User::query()->find($to)->unread_count_all;
            event(new MessageCountEvent($to_unread_count_all, $to));

            if (!empty($data['message'])) {
                $message_new = Message::create(['from' => $auth_id, 'to' => $to, 'message' => $data['message']]);
                $message = Message::with('from')->where(['id' => $message_new->id])->first();

                event(new RequestEvent($message, $params));
                event(new MessageEvent($message, $params));

                $FCM_res = Notification::sendNotification($device_tokens, $auth_user->full_name, $data['message'], [
                    'form' => $params, 'message_id' => $message_new->id]);
            } elseif (!empty($file)) {
                $message_new = Message::create(['from' => $auth_id, 'to' => $to]);
                $message_new->addMedia($request->file('file'))
                    ->toMediaCollection('file');

                $message = Message::with(['from'])->where(['id' => $message_new->id])->first();

                event(new RequestEvent($message, $params));
                event(new MessageEvent($message, $params));
                $_file = $message->toArray()['file'];
                $FCM_res = Notification::sendNotification($device_tokens, $auth_user->full_name, $_file['url'], [
                    'form' => $params, 'message_id' => $message_new->id], true);
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

        /** @var Message $message_from */
        $message_from = $user->messagesFrom()->where(['id' => $id])->first();

        /** @var Message $message_to */
        $message_to = $user->messagesTo()->where(['id' => $id])->first();

        if (!empty($message_from)) {
            $delete = ['from_deleted' => 1];

            if ($request->get('recipient') == 1) {
                $delete = array_merge($delete, ['to_deleted' => 1]);

                event(new MessageRemoveEvent($id, $user->id, $message_from->to));
            }

            $message_from->update($delete);
        } elseif (!empty($message_to)) {
            $message_to->update(['to_deleted' => 1]);
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

        /** @var Message $message_from */
        $message_from = $user->messagesFrom()->where(['to' => $id]);

        /** @var Message $message_to */
        $message_to = $user->messagesTo()->where(['from' => $id]);

        $messages = $request->get('messages');
        $text = '';

        try {
            if (!empty($messages)) {
                $validator = Validator::make($request->only('messages'), [
                    'messages' => 'required|array',
                    'messages.*' => 'integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $message_from->whereIn('id', $messages);
                $message_to->whereIn('id', $messages);

                $text = 'Сообщения удалены';
            }

            if ($message_from->exists()) {
                $delete = ['from_deleted' => 1];

                if ($request->get('recipient') == 1) {
                    $delete = array_merge($delete, ['to_deleted' => 1]);
                }

                $message_from->update($delete);
            }

            if ($message_to->exists()) {
                $message_to->update(['to_deleted' => 1]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendResponse(['message' => $text ?: 'Чат удален'], Response::HTTP_ACCEPTED);
    }
}
