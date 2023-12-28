<?php

use App\Constants\Status;
use App\Models\Education;
use App\Models\Program;
use App\Models\ProgramComplaint;
use App\Models\Specialist;
use App\Models\News;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

$auth = auth()->guard('admin')->user();
$education_count = Education::where('status', '<>', Status::ACTIVE)->count();
$for_checking_specialist_count = Specialist::whereNotIn('status', [Status::ACTIVE, Status::DELETED])->count();
$specialist_count = Specialist::whereIn('status', [Status::ACTIVE, Status::FOR_CHECKING, Status::DELETED])->count();
$program_count = Program::where('status', '<>', Status::ACTIVE)->count();
$media_videos = Media::query()->where(['collection_name' => 'video', 'custom_properties->status' => Status::PENDING])
    ->pluck('model_id')->toArray();
$video_count = Specialist::with('user')->where(['video_status' => Status::PENDING])->whereNotNull('video')
    ->orWhereIn('id', $media_videos)->count();
$complaint_count = ProgramComplaint::where(['status' => Status::PENDING])->count();
$all_count = $education_count + $for_checking_specialist_count + $program_count + $video_count + $complaint_count;
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{asset('assets/images/lifecoach.png')}}" alt="" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{config('app.name')}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{$auth->avatar?:asset('assets/dist/img/avatar.jpg')}}" class="img-circle elevation-2"
                     alt="User Image">
            </div>
            <div class="info">
                <a href="#"
                   class="d-block">{{sprintf('%s %s %s', $auth->first_name, $auth->last_name, $auth->patronymic_name)}}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item {{request()->is('dashboard*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link {{request()->is('dashboard*') ? 'active' : '' }}">
                        <i class="fa fa-question-circle nav-icon"></i>
                        <p>Для проверки
                            <span
                                class="badge badge-warning right" data-toggle="tooltip" data-placement="top"
                                title="Образование">{{$all_count}}</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/dashboard/specialist"
                               class="nav-link {{request()->is('dashboard/specialist') ? 'active' : '' }}">
                                <i class="fa fa-user-md nav-icon"></i>
                                <p>Специалисты
                                    <span
                                        class="badge badge-info right mr-1">{{$for_checking_specialist_count}}</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/education"
                               class="nav-link {{request()->is('dashboard/education') ? 'active' : '' }}">
                                <i class="fa fa-user-graduate nav-icon"></i>
                                <p>Образование
                                    <span
                                        class="badge badge-info right mr-1">{{$education_count}}</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/program"
                               class="nav-link {{request()->is('dashboard/program') ? 'active' : '' }}">
                                <i class="fal fa-lightbulb-on nav-icon"></i>
                                <p>Программы
                                    <span
                                        class="badge badge-info right mr-1">{{$program_count}}</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/program-complaint"
                               class="nav-link {{request()->is('dashboard/program-complaint') ? 'active' : '' }}">
                                <i class="far fa-angry nav-icon"></i>
                                <p>Жалобы
                                    <span
                                        class="badge badge-info right mr-1">{{$complaint_count}}</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/video"
                               class="nav-link {{request()->is('dashboard/video') ? 'active' : '' }}">
                                <i class="fal fa-video nav-icon"></i>
                                <p>Видео
                                    <span
                                        class="badge badge-info right mr-1">{{$video_count}}</span>
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">ПОЛЬЗОВАТЕЛИ</li>
                <li class="nav-item">
                    <a href="{{url('specialist')}}" class="nav-link {{request()->is('specialist') ? 'active' : '' }}">
                        <i class="fa fa-user-md nav-icon"></i>
                        <p>Специалисты
                            <span
                                class="badge badge-info right">{{$specialist_count}}</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{url('user')}}" class="nav-link {{request()->is('user') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p> Клиенты</p>
                    </a>
                </li>
                <li class="nav-header">НОВОСТИ</li>
                <li class="nav-item">
                    <a href="{{route('web-news-category.category.index')}}"
                       class="nav-link {{request()->is('category') ? 'active' : '' }}">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Категория</p>
                    </a>
                </li>
                <li class="nav-item {{request()->is('news*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link {{request()->is('news*') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-newspaper"></i>
                        <p> Новости
                            <span class="badge badge-info right">{{News::self()->count()}}</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('web-news.news.index')}}"
                               class="nav-link {{request()->is('news') ? 'active' : '' }}">
                                <i class="fa fa-list nav-icon"></i>
                                <p>Список</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('web-news.news.create')}}"
                               class="nav-link {{request()->is('news/create') ? 'active' : '' }}">
                                <i class="fa fa-plus nav-icon"></i>
                                <p>Добавить</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">СПЕЦИАЛЬНОСТЬ</li>
                <li class="nav-item">
                    <a href="{{route('web-specialty.specialty.index')}}"
                       class="nav-link {{request()->is('specialty') ? 'active' : '' }}">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Специальность</p>
                    </a>
                </li>

                <li class="nav-header">ГОРОДА</li>
                <li class="nav-item">
                    <a href="{{route('web-location.location.index')}}"
                       class="nav-link {{request()->is('location') ? 'active' : '' }}">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Города</p>
                    </a>
                </li>

                <li class="nav-header">ПРОГРАММЫ</li>
                <li class="nav-item">
                    <a href="{{route('web-program.program.index')}}"
                       class="nav-link {{request()->is('program') || request()->is('program/*')? 'active' : '' }}">
                        <i class="fal fa-lightbulb-on nav-icon"></i>
                        <p>Программы</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('web-program-category.program-category.index')}}"
                       class="nav-link {{request()->is('program-category') || request()->is('program-category/*') ? 'active' : '' }}">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Категории</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('web-program-chapter.chapter.index')}}"
                       class="nav-link {{request()->is('chapter') ? 'active' : '' }}">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Формат программы</p>
                    </a>
                </li>

                <li class="nav-header">СТРАНИЦЫ</li>
                <li class="nav-item">
                    <a href="{{route('page.index')}}"
                       class="nav-link {{request()->is('page') ? 'active' : '' }}">
                        <i class="fa fa-list nav-icon"></i>
                        <p>Страницы</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
