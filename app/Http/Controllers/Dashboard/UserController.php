<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Specialist;
use App\Models\User;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::query()->whereNotIn('id', Specialist::query()->select(['user_id'])->pluck('user_id')->toArray())->get();

        return view('dashboard.user.index', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findorFail($id);

        $from_message = Message::query()->where(['to' => $id])->groupBy('from')->pluck('from')->toArray();
        $to_message = Message::query()->where(['from' => $id])->groupBy('to')->pluck('to')->toArray();

        $message_count = Message::query()->where(['from' => $id])->orWhere(['to' => $id])->count();

        $users = User::query()->whereIn('id', array_unique(array_merge($from_message, $to_message)))
            ->select(['id', 'first_name', 'last_name', 'patronymic_name'])->paginate(10);

        return view('dashboard.user.profile', compact('user', 'message_count', 'users'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function messages(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if (!$from || !$to) {
            throw new NotFound();
        }

        $messages = Message::query()->where(['from' => $from, 'to' => $to])->with(['from', 'to'])->orWhere(function($query) use ($from, $to) {
            $query->where(['from' => $to, 'to' => $from]);
        })->orderBy('id', 'desc')->paginate(10);

        return response()->json(compact('messages'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findorFail($id)->delete();
        session()->flash('success', 'Пользователь успешно удален');

        return back();
    }
}
