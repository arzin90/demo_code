<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Program;

class ProgramController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard.program.index', [
            'programs' => Program::query()->with([
                'specialist', 'categories', 'location',
            ])->get(),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $program = Program::query()->where(['id' => $id])->with([
            'specialist', 'categories', 'location', 'users', 'gallery', 'programDates', 'programChapters',
        ])->first();

        if (empty($program)) {
            return redirect('/dashboard/specialist');
        }

        return view('dashboard.program.show', compact('program'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        Program::query()->where(['id' => $id])->update(['status' => Status::ACTIVE]);
        session()->flash('success', 'Программа успешно активирована.');

        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pending($id)
    {
        Program::query()->where(['id' => $id])->update(['status' => Status::PENDING]);
        session()->flash('success', 'Программа успешно добавлено в ожидание.');

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
        $program = Program::query()->where(['id' => $id]);

        if ($program->exists()) {
            $program->first()->clearMediaCollection('gallery')->delete();
        }

        session()->flash('success', 'Программа успешно удален.');

        return back();
    }
}
