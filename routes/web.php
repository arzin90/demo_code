<?php

use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ChapterController;
use App\Http\Controllers\Dashboard\EducationController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\NewsController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\ProgramCategoryController;
use App\Http\Controllers\Dashboard\ProgramComplaintController;
use App\Http\Controllers\Dashboard\ProgramController;
use App\Http\Controllers\Dashboard\SpecialistController;
use App\Http\Controllers\Dashboard\SpecialtyController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('email/verify/{userId}', 'App\Http\Controllers\VerificationController@verify')->name('verification.verify');

Route::match(['get', 'post'], '/login', [MainController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:admin'], function() {
    Route::get('/dashboard/{type}', [MainController::class, 'index'])->name('dashboard');
    Route::get('/logout', [MainController::class, 'logout'])->name('logout');

    /* User */
    Route::get('/user/messages', [UserController::class, 'messages']);
    Route::resource('user', UserController::class);

    /* Specialist */
    Route::name('web-specialist')->resource('specialist', SpecialistController::class);
    Route::post('specialist/{id}/active', [SpecialistController::class, 'active']);
    Route::post('specialist/{id}/pending', [SpecialistController::class, 'pending']);
    Route::post('specialist/{id}/video/pending', [SpecialistController::class, 'videoPending']);
    Route::post('specialist/{id}/video/active', [SpecialistController::class, 'videoActive']);
    Route::post('specialist/video/{id}/pending', [SpecialistController::class, 'videoMediaPending']);
    Route::post('specialist/video/{id}/active', [SpecialistController::class, 'videoMediaActive']);
    Route::delete('specialist/{id}/video', [SpecialistController::class, 'removeVideo']);

    /* Education */
    Route::name('web-education')->resource('education', EducationController::class)->only(['destroy']);
    Route::post('education/{id}/active', [EducationController::class, 'active']);
    Route::post('education/{id}/pending', [EducationController::class, 'pending']);

    /* Specialty */
    Route::name('web-specialty')->resource('specialty', SpecialtyController::class);
    Route::post('specialty/{id}/active', [SpecialtyController::class, 'active']);
    Route::post('specialty/{id}/pending', [SpecialtyController::class, 'pending']);

    /* News */
    Route::name('web-news')->resource('news', NewsController::class);
    Route::post('news/{id}/publish', [NewsController::class, 'publish']);
    Route::post('news/{id}/pending', [NewsController::class, 'pending']);

    /* News category */
    Route::name('web-news-category')->resource('category', CategoryController::class);

    /* Location */
    Route::name('web-location')->resource('location', LocationController::class)->only(['index', 'update']);

    /* Page */
    Route::resource('page', PageController::class);
    Route::post('page/{id}/active', [PageController::class, 'active']);
    Route::post('page/{id}/pending', [PageController::class, 'pending']);

    /* Program */
    Route::name('web-program')->resource('program', ProgramController::class)->only(['index', 'show', 'destroy']);
    Route::post('program/{id}/active', [ProgramController::class, 'active']);
    Route::post('program/{id}/pending', [ProgramController::class, 'pending']);

    /* Program Chapter */
    Route::name('web-program-chapter')->resource('chapter', ChapterController::class);
    Route::post('chapter/{id}/active', [ChapterController::class, 'active']);
    Route::post('chapter/{id}/pending', [ChapterController::class, 'pending']);

    /* Program Category */
    Route::name('web-program-category')->resource('program-category', ProgramCategoryController::class);
    Route::post('program-category/{id}/active', [ProgramCategoryController::class, 'active']);
    Route::post('program-category/{id}/pending', [ProgramCategoryController::class, 'pending']);

    /* Program Complaint */
    Route::name('web-program-complaint')->resource('program-complaint', ProgramComplaintController::class)->only(['destroy']);
});
