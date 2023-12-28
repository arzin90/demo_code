<?php

use App\Constants\Status;

$diploma_images = [];
?>
@extends('layouts.dashboard')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{!empty($program->specialist) && !empty($program->specialist->user->avatar_url) ? $program->specialist->user->avatar_url:asset('assets/dist/img/avatar.jpg')}}"
                                     alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">{{!empty($program->specialist)?$program->specialist->user->full_name:'Не указано'}}</h3>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Статус</b> <a
                                        class="float-right">{!! !empty($program->specialist)?\App\Helper\Badge::getByStatusName($program->specialist->status):'Не указано'!!}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>E-Mail</b> <a
                                        class="float-right">{{!empty($program->specialist)?$program->specialist->user->email:'Не указано'}}</a>
                                </li>
                                @if(!empty($program->specialist) && $program->specialist->user->phone)
                                    <li class="list-group-item">
                                        <b>Телефон</b> <a class="float-right">{{$program->specialist->user->phone}}</a>
                                    </li>
                                @endif
                                @if(!empty($program->specialist) && $program->specialist->user->gender)
                                    <li class="list-group-item">
                                        <b>Пол</b> <a
                                            class="float-right">{{$program->specialist->user->gender=='male'? 'Мужской': 'Женский'}}</a>
                                    </li>
                                @endif
                                @if(!empty($program->specialist) && $program->specialist->user->b_day)
                                    <li class="list-group-item">
                                        <b>Дата рождения</b> <a
                                            class="float-right">{{$program->specialist->user->b_day}}</a>
                                    </li>
                                @endif
                                @if(!empty($program->specialist) && $program->specialist->user->url)
                                    <li class="list-group-item">
                                        <b>URL</b> <a href="{{$program->specialist->user->url}}" target="_blank"
                                                      class="float-right">{{$program->specialist->user->url}}</a>
                                    </li>
                                @endif
                                @if(!empty($program->specialist) && $program->specialist->user->verified_at)
                                    <li class="list-group-item text-sm">
                                        <b>Зарегистрирован</b> <a
                                            class="float-right">{{$program->specialist->user->verified_at}}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- About Me Box -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Обо мне</h3>
                        </div>

                        @if(!empty($program->specialist) && $program->specialist->user->location)
                            <!-- /.card-header -->
                            <div class="card-body">
                                <strong><i class="fas fa-map-marker-alt mr-1"></i>Расположение</strong>
                                <p class="text-muted">
                                    {{$program->specialist->user->location->city}}
                                </p>
                            </div>
                            <!-- /.card-body -->
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#about_program" data-toggle="tab">О программе</a>
                                </li>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#users" id="users_tab" data-toggle="tab">Участники</a>
                                </li>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#gallery" data-toggle="tab">Галерея</a>
                                </li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- /.tab-pane -->
                                <div class="tab-pane active" id="about_program">
                                    <div class="text-left">
                                        <div class="card-body">
                                            <div class="row">
                                                <div>
                                                    <strong>Название</strong>
                                                    <br/> {{$program->name}}
                                                    <br/>
                                                    <br/>
                                                    <strong>{{ $program->is_online ? 'Онлайн' : 'Офлайн'}} </strong>
                                                    <br/>{{ !empty($program->location) ? $program->location->city : (empty($program->link) ? 'Ссылка будет скинута позже' : '') }}
                                                    @if(!empty($program->link))
                                                        <strong>Ссылка</strong>
                                                        <br/> {{ $program->link }}
                                                    @endif
                                                    <br/>
                                                    <br/>
                                                    @if(!$program->categories->isEmpty())
                                                        <strong>Категория</strong>
                                                        <br/>{{$program->categories->pluck('name')->join(', ')}}
                                                        <br/>
                                                        <br/>
                                                    @endif
                                                    @if(!empty($program->programChapters))
                                                        <strong>Формат программы</strong>
                                                        <ul>
                                                            @foreach($program->programChapters as $chapter)
                                                                <li> {{$chapter->name}} </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                    <strong>Описание</strong>
                                                    <br/>
                                                    @if($program->description)
                                                        {!! nl2br($program->description) !!}
                                                    @else
                                                        (Без содержания)
                                                    @endif
                                                    <br/>
                                                    <br/>
                                                    <strong>Цена программы</strong>
                                                    <br/>{{$program->price}}
                                                    <br/>
                                                    <br/>
                                                    <strong>Ведущий программы</strong>
                                                    <br/>{{$program->presenter?: (!empty($program->presenter_user) && $program->presenter_user->full_name ? $program->presenter_user->full_name : 'Не указано')}}
                                                    <br/>
                                                    <br/>
                                                    @if($program->sale_price)
                                                        <strong>Цена продажи</strong>
                                                        <br/>{{$program->sale_price}}
                                                        <br/>
                                                        <br/>
                                                    @endif

                                                    <strong>Дата и время</strong>
                                                    <br/>
                                                    @if(!empty($program->programDates) && $program->programDates->count())
                                                        <ul>
                                                                <?php $dates = collect($program->programDates)->sortBy('date')->groupBy('date')->all() ?>
                                                            @foreach($dates as $key => $date)
                                                                @if(!empty($dates[$key]) && $dates[$key]->count())
                                                                    <li> {{$key}}
                                                                        <ul>
                                                                            @foreach($date as $time)
                                                                                @if($time->start_time && $time->end_time)
                                                                                    <li>{{sprintf('%s - %s', $time->start_time, $time->end_time)}}</li>
                                                                                @else
                                                                                    Любое время
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                @else
                                                                    Любое время
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        Любая дата/Любое время
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="users">
                                    <div class="text-left">
                                        <div class="card-body">
                                            <table id="data_table_users" class="table table-bordered table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Имя</th>
                                                    <th>Фамилия</th>
                                                    <th>Отчество</th>
                                                    <th>Эл. почта</th>
                                                    <th>Телефон</th>
                                                    <th>Статус</th>
                                                    <th>Зарегистрирован</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!empty($program->users))
                                                    @foreach($program->users as $user)
                                                        <tr>
                                                            <td>{{$user->first_name}}</td>
                                                            <td>{{$user->last_name}}</td>
                                                            <td>{{$user->patronymic_name}}</td>
                                                            <td>{{$user->email}}</td>
                                                            <td>{{$user->phone}}</td>
                                                            <td>{!! \App\Helper\Badge::getByStatusId($user->status_id)!!}</td>
                                                            <td>{{$user->verified_at}}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{$url = url("/user/{$user->id}")}}"
                                                                       class="btn btn-info">
                                                                        <i class="fa fa-eye"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="gallery">
                                    <div class="text-left">
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($program->getMedia('gallery') as $image)
                                                    <div class="col-sm-3">
                                                        <a href="{{$image->getFullUrl()}}"
                                                           data-toggle="lightbox"
                                                           data-gallery="gallery">
                                                            <img src="{{$image->getFullUrl()}}"
                                                                 class="img-fluid mb-2" alt="white sample"/>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
    @include('dashboard/partials/css/dataTable')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    @include('dashboard/partials/js/dataTable')
    <script>
        $(function () {
            let userTabActive = false;

            $(document).on('click', '#users_tab', function () {
                if (!userTabActive) {
                    $("#data_table_users").DataTable({
                        language: {
                            url: '{{asset('assets/lang/datatable.ru.json')}}'
                        },
                        "responsive": true, "lengthChange": false, "autoWidth": false
                    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                    userTabActive = true
                }
            })
        })
    </script>
@endsection
