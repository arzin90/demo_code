<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class CategoryController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard.news.category.index', ['categories' => Category::all()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('dashboard.news.category.create');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function store(Request $request)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255|unique:categories',
        ]);

        if ($validator->fails()) {
            return redirect('category/create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $news = new Category($data);

            $news->save();
            $request->session()->flash('success', 'Категория успешно создан.');
        } catch (\Exception $e) {
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('category');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        return view('dashboard.news.category.edit', ['category' => Category::findorFail($id)]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws InternalErrorException
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('name');

        $category = Category::findorFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255|unique:categories,name,'.$id,
        ]);

        if ($validator->fails()) {
            return redirect(sprintf('category/%d/edit', $id))
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $category->update($data);

            $request->session()->flash('success', 'Категория успешно обновлен.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('category');
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
        Category::findorFail($id)->delete();
        session()->flash('success', 'Категория успешно удален.');

        return back();
    }
}
