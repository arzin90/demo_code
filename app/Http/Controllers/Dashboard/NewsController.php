<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class NewsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard.news.index', ['news' => News::with(['categories'])->get()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('dashboard.news.create', [
            'statusList' => News::getStatus(),
            'categories' => Category::getList(),
            'locations' => Location::getList(),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function store(Request $request)
    {
        $data = $request->only('title', 'short_description', 'description', 'status_id', 'location_id', 'category', 'image');

        $validator = Validator::make($data, [
            'title' => 'required|string|min:2|max:255|unique:news',
            'short_description' => 'required|string|min:2|max:2000',
            'description' => 'required|string|min:2',
            'status_id' => sprintf('required|in:%s', implode(',', array_keys(News::getStatus()))),
            'location_id' => 'nullable|numeric|exists:locations,id',
            'category' => 'required|numeric|exists:categories,id',
            'image' => 'required|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect('news/create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $news = News::create($data);
            $news->newsCategory()->create(['category_id' => $data['category']]);

            if ($request->has('image') && $request->file('image')) {
                $news->addMedia($request->file('image'))
                    ->toMediaCollection('image');
            }

            $news->save();
            DB::commit();
            $request->session()->flash('success', 'Новости успешно создан.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('news');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        return view('dashboard.news.edit', [
            'news' => News::findorFail($id),
            'categories' => Category::getList(),
            'statusList' => News::getStatus(),
            'locations' => Location::getList(),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('title', 'short_description', 'description', 'location_id', 'category', 'status_id', 'image');

        $news = News::findorFail($id);

        $validator = Validator::make($data, [
            'title' => 'required|string|min:2|max:255|unique:news,title,'.$id,
            'short_description' => 'required|string|min:2|max:2000',
            'description' => 'required|string|min:2',
            'status_id' => sprintf('required|in:%s', implode(',', array_keys(News::getStatus()))),
            'location_id' => 'nullable|numeric|exists:locations,id',
            'category' => 'required|numeric|exists:categories,id',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect(sprintf('news/%d/edit', $id))
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $news->update($data);
            $news->newsCategory()->update(['category_id' => $data['category']]);
            if ($request->has('image') && $request->file('image')) {
                $news->clearMediaCollection('image');
                $news->addMedia($request->file('image'))
                    ->toMediaCollection('image');
            }

            DB::commit();
            $request->session()->flash('success', 'Новости успешно обновлен.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('news');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish($id)
    {
        News::findorFail($id)->update(['status_id' => News::STATUS_ACTIVE]);
        session()->flash('success', 'Новости успешно опубликован.');

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pending($id)
    {
        News::findorFail($id)->update(['status_id' => News::STATUS_PENDING]);
        session()->flash('success', 'Новости успешно добавлено в ожидание.');

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
        News::findorFail($id)->clearMediaCollection('image')->delete();
        session()->flash('success', 'Новости успешно удален.');

        return back();
    }
}
