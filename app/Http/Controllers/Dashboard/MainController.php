<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Program;
use App\Models\ProgramComplaint;
use App\Models\Specialist;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MainController extends Controller
{
    /**
     * @param string $type (specialist, education, program)
     *
     * @return Application|Factory|View
     */
    public function index($type)
    {
        if ($type == 'specialist') {
            $specialists = Specialist::with('user')->whereNotIn('status', [Status::ACTIVE, Status::DELETED])->get();

            return view('dashboard.for-checking.specialist', compact('specialists'));
        }

        if ($type == 'education') {
            $educations = Education::with('specialist')->where('status', '<>', Status::ACTIVE)->get();

            return view('dashboard.for-checking.education', compact('educations'));
        }

        if ($type == 'program') {
            $programs = Program::query()->with([
                'specialist', 'categories', 'location',
            ])->where('status', '<>', Status::ACTIVE)->get();

            return view('dashboard.for-checking.program', compact('programs'));
        }

        if ($type == 'program-complaint') {
            $program_complaints = ProgramComplaint::with(['user', 'program'])->get();

            return view('dashboard.for-checking.program-complaint', compact('program_complaints'));
        }

        if ($type == 'video') {
            $media_videos = Media::query()->where(['collection_name' => 'video', 'custom_properties->status' => Status::PENDING])
                ->pluck('model_id')->toArray();
            $videos = Specialist::with('user')->where(['video_status' => Status::PENDING])->whereNotNull('video')
                ->orWhereIn('id', $media_videos)->get();

            return view('dashboard.for-checking.video', compact('videos'));
        }

        abort(404);
    }

    /**
     * @return Application|Factory|View|RedirectResponse|Redirector
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = $request->only('email', 'password');

            $validator = Validator::make($credentials, [
                'email' => 'required|email:dns|max:255',
                'password' => 'required|string|max:200',
            ]);

            if ($validator->fails()) {
                return redirect('login')
                    ->withErrors($validator)
                    ->withInput();
            }

            if (Auth::guard('admin')->attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->intended('dashboard/specialist');
            }
            session()->flash('invalid', 'Неверный почта или пароль');

            return back();
        }

        return view('dashboard.login');
    }

    /**
     * Logout action
     */
    public function logout()
    {
        auth()->guard('admin')->logout();

        return redirect('login');
    }
}
