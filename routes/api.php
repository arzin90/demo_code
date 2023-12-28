<?php

use App\Api\V1\Controllers\AuthController;
use App\Api\V1\Controllers\EducationController;
use App\Api\V1\Controllers\FavoriteController;
use App\Api\V1\Controllers\FeedController;
use App\Api\V1\Controllers\GroupController;
use App\Api\V1\Controllers\GroupMessageController;
use App\Api\V1\Controllers\LocationController;
use App\Api\V1\Controllers\MessageController;
use App\Api\V1\Controllers\NewsController;
use App\Api\V1\Controllers\PageController;
use App\Api\V1\Controllers\ProgramCommentController;
use App\Api\V1\Controllers\ProgramComplaintController;
use App\Api\V1\Controllers\ProgramController;
use App\Api\V1\Controllers\ProgramFavoriteController;
use App\Api\V1\Controllers\ProgramRateController;
use App\Api\V1\Controllers\RateController;
use App\Api\V1\Controllers\SpecialistCommentController;
use App\Api\V1\Controllers\SpecialistController;
use App\Api\V1\Controllers\SpecialistSubscriptionController;
use App\Api\V1\Controllers\SpecialtyController;
use App\Api\V1\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function($route) {
    Route::group(['prefix' => 'auth'], function() {
        Route::get('user', [AuthController::class, 'user']);
        Route::delete('user', [AuthController::class, 'destroy']);
        Route::delete('refresh-token', [AuthController::class, 'refresh'])->name('refresh-token');
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('check-email', [AuthController::class, 'checkRegisteredEmail']);
        Route::post('register-or-login', [AuthController::class, 'registerOrLogin']);
        Route::post('check-code', [AuthController::class, 'checkCode']);
        Route::post('after-confirm', [AuthController::class, 'afterConfirm']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('mute', [AuthController::class, 'mute']);
        Route::post('unmute', [AuthController::class, 'unmute']);
        Route::post('last-visit', [AuthController::class, 'lastVisit']);
    });

    Route::middleware(['auth:api', 'account.access'])->group(function() {
        Route::group(['prefix' => 'user'], function() {
            Route::post('change-password', [UserController::class, 'changePassword']);
            Route::post('change-email-or-phone', [UserController::class, 'changeEmailOrPhone']);
            Route::post('confirm-email-or-phone', [UserController::class, 'confirmEmailOrPhone']);
            Route::post('location', [UserController::class, 'location']);
            Route::get('search', [UserController::class, 'search']);
            Route::get('search-client-and-specialist', [UserController::class, 'searchClientAndSpecialist'])->middleware('specialist.access');
            Route::get('specialists', [UserController::class, 'specialists']);
            Route::get('my-specialist', [UserController::class, 'mySpecialist']);
            Route::post('specialist/find', [UserController::class, 'findSpecialist']);
            Route::post('add-specialist', [UserController::class, 'addSpecialist']);
            Route::delete('my-specialist/{id}', [UserController::class, 'deleteSpecialist']);
            Route::get('{id}', [UserController::class, 'show'])->where('id', '[0-9]+');
            Route::post('become-specialist', [UserController::class, 'becomeToSpecialist']);
            Route::post('{id}/mute', [UserController::class, 'mute'])->where('id', '[0-9]+');
            Route::post('{id}/unmute', [UserController::class, 'unmute'])->where('id', '[0-9]+');
        });
        Route::group(['prefix' => 'profile'], function() {
            Route::group(['prefix' => 'edit'], function() {
                Route::post('/', [UserController::class, 'update']);
            });

            Route::match(['get', 'post'], '/about', [UserController::class, 'about']);

            Route::delete('image', [UserController::class, 'deleteImage']);
        });

        /* Education */
        Route::get('education/documents', [EducationController::class, 'documents']);
        Route::resource('education', EducationController::class);
        Route::post('education/{id}', [EducationController::class, 'update'])->where('id', '[0-9]+');
        Route::post('education/documents', [EducationController::class, 'addDocument']);
        Route::delete('education/documents/{id}', [EducationController::class, 'deleteDocument'])->where('id', '[0-9]+');

        /* Specialty */
        Route::group(['prefix' => 'specialty'], function() {
            Route::get('/', [SpecialtyController::class, 'index']);
            Route::post('/', [SpecialtyController::class, 'store']);
        });

        /* Specialist access */
        Route::middleware('specialist.access')->group(function() {
            /* Specialist */
            Route::resource('specialist', SpecialistController::class)->only(['index', 'store']);
            Route::group(['prefix' => 'specialist'], function() {
                Route::post('become-client', [SpecialistController::class, 'becomeToClient']);
                Route::get('work-type', [SpecialistController::class, 'workType']);
                Route::post('online', [SpecialistController::class, 'online']);
                Route::post('offline', [SpecialistController::class, 'offline']);
                Route::get('video', [SpecialistController::class, 'videos']);
                Route::post('video', [SpecialistController::class, 'addVideo']);
                Route::delete('video', [SpecialistController::class, 'deleteVideo']);

                /* Client */
                Route::get('client', [SpecialistController::class, 'searchClient']);
                Route::post('client/find', [SpecialistController::class, 'findClient']);
                Route::get('client/{id}', [SpecialistController::class, 'getClient']);
                Route::post('client', [SpecialistController::class, 'addClient']);
                Route::post('client/notify', [SpecialistController::class, 'notifyClient']);
                Route::post('client/{id}', [SpecialistController::class, 'updateClient']);
                Route::match(['get', 'post'], 'client/{id}/about', [SpecialistController::class, 'aboutClient']);
                Route::delete('client/{id}', [SpecialistController::class, 'deleteClient']);

                /* Specialist Programs */
                Route::get('{id}/programs', [SpecialistController::class, 'programs'])->withoutMiddleware('specialist.access');

                /* Programs */
                Route::resource('program', ProgramController::class)->only(['store', 'destroy']);
                Route::group(['prefix' => 'program'], function() {
                    Route::get('my', [ProgramController::class, 'my']);
                    Route::get('chapter', [ProgramController::class, 'chapter']);
                    Route::get('{id}/users', [ProgramController::class, 'user'])->where('id', '[0-9]+');
                    Route::post('{id}/users', [ProgramController::class, 'addUsers'])->where('id', '[0-9]+');
                    Route::post('{id}', [ProgramController::class, 'update'])->where('id', '[0-9]+');
                    Route::post('{id}/see', [ProgramController::class, 'specialistSee'])->where('id', '[0-9]+');
                    Route::post('{id}/activate', [ProgramController::class, 'activate'])->where('id', '[0-9]+');
                    Route::post('{id}/inactivate', [ProgramController::class, 'inactivate'])->where('id', '[0-9]+');
                });
            });
        });

        /* Programs */
        Route::group(['prefix' => 'program'], function() {
            Route::get('my', [ProgramController::class, 'myJoin']);
            Route::get('category', [ProgramController::class, 'category']);
            Route::post('{id}/join', [ProgramController::class, 'join']);
            Route::post('{id}/book', [ProgramController::class, 'book']);
            Route::post('see', [ProgramController::class, 'see']);
            Route::delete('{id}/leave', [ProgramController::class, 'leave']);
            /* Rate */
//            Route::post('{program_id}/rate', [ProgramRateController::class, 'store']);
//            Route::delete('{program_id}/rate', [ProgramRateController::class, 'destroy']);
            /* Comment */
            Route::get('{id}/comment', [ProgramCommentController::class, 'index']);
            Route::post('{id}/comment', [ProgramCommentController::class, 'store']);
            Route::put('comment/{id}', [ProgramCommentController::class, 'update']);
            Route::delete('comment/{id}', [ProgramCommentController::class, 'destroy']);
            /* Favorite */
            Route::get('favorite', [ProgramFavoriteController::class, 'index']);
            Route::post('{program}/favorite', [ProgramFavoriteController::class, 'store']);
            Route::delete('{program}/favorite', [ProgramFavoriteController::class, 'destroy']);
        });
        Route::resource('program', ProgramController::class)->only(['index', 'show']);

        /* Program Complaints */
        Route::apiResource('program/{program}/complaint', ProgramComplaintController::class);

        /* Chat Group */
        Route::group(['prefix' => 'group'], function() {
            Route::get('/', [GroupController::class, 'index']);
            Route::get('{id}', [GroupController::class, 'show'])->where('id', '[0-9]+');
            Route::get('my', [GroupController::class, 'my'])->middleware('specialist.access');
            Route::get('{id}/members', [GroupController::class, 'showMembers']);
            Route::post('/', [GroupController::class, 'store'])->middleware('specialist.access');
            Route::post('{id}/member', [GroupController::class, 'addMember'])->middleware('specialist.access')->where('id', '[0-9]+');
            Route::post('{id}', [GroupController::class, 'update'])->middleware('specialist.access')->where('id', '[0-9]+');
            Route::post('{id}/mute', [GroupController::class, 'mute'])->where('id', '[0-9]+');
            Route::post('{id}/unmute', [GroupController::class, 'unmute'])->where('id', '[0-9]+');
            Route::delete('{id}', [GroupController::class, 'destroy'])->where('id', '[0-9]+');
            Route::delete('{id}/member/leave', [GroupController::class, 'leaveMember'])->where('id', '[0-9]+');
            Route::delete('{id}/member/{member_id}', [GroupController::class, 'removeMember'])->middleware('specialist.access')
                ->where('id', '[0-9]+')->where('member_id', '[0-9]+');

            /* Chat Group Message */
            Route::get('/{id}/messages', [GroupMessageController::class, 'index']);
            Route::post('/{id}/message', [GroupMessageController::class, 'store']);
            Route::delete('/{id}/messages', [GroupMessageController::class, 'destroyAll']);
            Route::delete('/message/{id}', [GroupMessageController::class, 'destroy']);
        });

        /* Favorite Group */
        Route::group(['prefix' => 'favorite'], function() {
            Route::get('/', [FavoriteController::class, 'index']);
            Route::post('{specialist}', [FavoriteController::class, 'store']);
            Route::delete('{specialist}', [FavoriteController::class, 'destroy']);
        });

        /* Rate */
//        Route::group(['prefix' => 'rate'], function () {
//            Route::post('{specialist_id}', [RateController::class, 'store']);
//            Route::delete('{specialist_id}', [RateController::class, 'destroy']);
//        });

        /* Specialist Comment */
        Route::group(['prefix' => 'comment'], function() {
            Route::get('specialist/{id}', [SpecialistCommentController::class, 'index']);
            Route::post('specialist/{id}', [SpecialistCommentController::class, 'store']);
            Route::put('{id}', [SpecialistCommentController::class, 'update']);
            Route::delete('{id}', [SpecialistCommentController::class, 'destroy']);
        });

        /* News */
        Route::resource('news', NewsController::class)->only(['index', 'show']);

        /* Feed */
        Route::group(['prefix' => 'feed'], function() {
            Route::get('specialist', [FeedController::class, 'specialist']);
            Route::get('program', [FeedController::class, 'program']);
        });

        /* Location */
        Route::resource('location', LocationController::class)->only(['index']);

        /* Messages */
        Route::group(['prefix' => 'messages'], function() {
            Route::get('/', [MessageController::class, 'search']);
            Route::delete('{id}', [MessageController::class, 'destroy'])->where('id', '[0-9]+');

            Route::group(['prefix' => 'user'], function() {
                Route::get('/', [MessageController::class, 'users']);
                Route::get('{id}', [MessageController::class, 'index'])->where('id', '[0-9]+');
                Route::post('{id}', [MessageController::class, 'store'])->where('id', '[0-9]+');
                Route::delete('{id}', [MessageController::class, 'destroyAll'])->where('id', '[0-9]+');
            });
        });

        /* Specialist Subscription */
        Route::group(['prefix' => 'subscribe/specialist'], function() {
            Route::get('/subscribers', [SpecialistSubscriptionController::class, 'subscribers'])->middleware('specialist.access');
            Route::get('{id}/subscribers', [SpecialistSubscriptionController::class, 'subscribersBySpecialist']);
            Route::get('/', [SpecialistSubscriptionController::class, 'index']);
            Route::get('{spec}', [SpecialistSubscriptionController::class, 'subscription']);
            Route::post('{id}', [SpecialistSubscriptionController::class, 'subscribe']);
            Route::delete('{id}', [SpecialistSubscriptionController::class, 'unsubscribe']);
        });
    });

    /* Page */
    Route::get('page/{slug}', [PageController::class, 'bySlug']);
});
