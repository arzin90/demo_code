<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class PageController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard.page.index', ['pages' => Page::all()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('dashboard.page.create', [
            'statusList' => [Status::PENDING => 'На проверку', Status::ACTIVE => 'Активный'],
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function store(Request $request)
    {
        $data = $request->only(['title', 'status', 'key', 'content']);

        $validator = Validator::make($data, [
            'title' => 'nullable|string|min:2|max:255|unique:pages',
            'status' => sprintf('required|in:%s', implode(',', [Status::PENDING, Status::ACTIVE])),
            'key' => 'required|alpha_dash|min:2|max:255|unique:pages',
            'content' => 'nullable|string|min:2',
        ]);

        if ($validator->fails()) {
            return redirect('page/create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $page = new Page($data);

            $page->save();
            $request->session()->flash('success', 'Страница успешно создан.');
        } catch (\Exception $e) {
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('page');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        return view('dashboard.page.edit', [
            'page' => Page::findorFail($id),
            'statusList' => [Status::PENDING => 'На проверку', Status::ACTIVE => 'Активный']]
        );
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function update(Request $request, $id)
    {
        $data = $request->only(['title', 'status', 'key', 'content']);

        $specialty = Page::findorFail($id);

        $validator = Validator::make($data, [
            'title' => 'nullable|string:lowerCase|min:2|max:255|unique:pages,title,'.$id,
            'status' => sprintf('required|in:%s', implode(',', [Status::PENDING, Status::ACTIVE])),
            'key' => 'required|alpha_dash|min:2|max:255|unique:pages,key,'.$id,
            'content' => 'nullable|string|min:2',
        ]);

        if ($validator->fails()) {
            return redirect(sprintf('page/%d/edit', $id))
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $specialty->update($data);

            $request->session()->flash('success', 'Страница успешно обновлен.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('page');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        Page::findorFail($id)->update(['status' => Status::ACTIVE]);
        session()->flash('success', 'Страница успешно активирован.');

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pending($id)
    {
        Page::findorFail($id)->update(['status' => Status::PENDING]);
        session()->flash('success', 'Страница успешно добавлено в ожидание.');

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
        Page::findorFail($id)->delete();
        session()->flash('success', 'Страница успешно удален.');

        return back();
    }
}
