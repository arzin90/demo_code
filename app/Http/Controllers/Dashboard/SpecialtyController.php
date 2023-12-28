<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class SpecialtyController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('dashboard.specialty.index', ['specialties' => Specialty::all()]);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('dashboard.specialty.create');
    }

    /**
     * @return Application|RedirectResponse|Redirector
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
            return redirect('specialty/create')
                ->withErrors($validator)
                ->withInput();
        }

        $data['status'] = Status::ACTIVE;

        try {
            $news = new Specialty($data);

            $news->save();
            $request->session()->flash('success', 'Специальность успешно создан.');
        } catch (\Exception $e) {
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('specialty');
    }

    /**
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        return view('dashboard.specialty.edit', ['specialty' => Specialty::findorFail($id)]);
    }

    /**
     * @return Application|RedirectResponse|Redirector
     *
     * @throws InternalErrorException
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('name');

        $specialty = Specialty::findorFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|string|min:2|max:255|unique:specialties,name,'.$id,
        ]);

        if ($validator->fails()) {
            return redirect(sprintf('specialty/%d/edit', $id))
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $specialty->update($data);

            $request->session()->flash('success', 'Специальность успешно обновлена.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InternalErrorException($e->getMessage());
        }

        return redirect('specialty');
    }

    /**
     * @return RedirectResponse
     */
    public function active($id)
    {
        Specialty::findorFail($id)->update(['status' => Status::ACTIVE]);
        session()->flash('success', 'Специальность успешно активирован.');

        return back();
    }

    /**
     * @return RedirectResponse
     */
    public function pending($id)
    {
        Specialty::findorFail($id)->update(['status' => Status::PENDING]);
        session()->flash('success', 'Специальность успешно добавлено в ожидание.');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        Specialty::findorFail($id)->delete();
        session()->flash('success', 'Специальность успешно удален.');

        return back();
    }
}
