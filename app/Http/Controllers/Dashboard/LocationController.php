<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard.location.index', ['locations' => Location::query()->orderBy('popular', 'desc')->orderBy('city')->get()]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('popular');

        $validator = Validator::make($data, [
            'popular' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            session()->flash('error', $validator->getMessageBag()->get('popular')[0]);

            return redirect('location');
        }

        Location::findorFail($id)->update($data);
        session()->flash('success', 'Успешно сохранено.');

        return back();
    }
}
