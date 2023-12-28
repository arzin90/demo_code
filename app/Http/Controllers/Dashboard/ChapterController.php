<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class ChapterController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard.chapter.index', ['chapters' => Chapter::all()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('dashboard.chapter.create', ['statusList' => Chapter::getStatus(), 'typeList' => Chapter::getType()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function store(Request $request)
    {
        $data = $request->only('name', 'status', 'type');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255|unique:chapters',
            'status' => 'required|in:active,pending',
            'type' => 'nullable|in:online,offline',
        ]);

        if ($validator->fails()) {
            return redirect('chapter/create')
                ->withErrors($validator)
                ->withInput();
        }

        $data['status'] = Status::ACTIVE;

        try {
            Chapter::create($data);

            $request->session()->flash('success', 'Формат программы успешно создан.');
        } catch (\Exception $e) {
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('chapter');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        return view('dashboard.chapter.edit', ['chapter' => Chapter::findorFail($id), 'statusList' => Chapter::getStatus(), 'typeList' => Chapter::getType()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('name', 'status', 'type');

        $chapter = Chapter::findorFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255|unique:chapters,name,'.$id,
            'status' => 'required|in:active,pending',
            'type' => 'nullable|in:online,offline',
        ]);

        if ($validator->fails()) {
            return redirect(sprintf('chapter/%d/edit', $id))
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $chapter->update($data);

            $request->session()->flash('success', 'Формат программы успешно обновлен.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('chapter');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        Chapter::findorFail($id)->update(['status' => Status::ACTIVE]);
        session()->flash('success', 'Раздел успешно активирован.');

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pending($id)
    {
        Chapter::findorFail($id)->update(['status' => Status::PENDING]);
        session()->flash('success', 'Раздел успешно добавлено в ожидание.');

        return back();
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
        Chapter::findorFail($id)->delete();
        session()->flash('success', 'Раздел успешно удален.');

        return back();
    }
}
