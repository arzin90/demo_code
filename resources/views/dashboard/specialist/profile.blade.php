<?php
use App\Constants\Status;

$diploma_images = [];
?>
@extends('layouts.dashboard')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{$specialist->user->avatar_url}}"
                                     alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">{{$specialist->user->full_name}}</h3>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Статус</b> <a
                                        class="float-right">{!! \App\Helper\Badge::getByStatusName($specialist->status) !!}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>E-Mail</b> <a class="float-right">{{$specialist->user->email}}</a>
                                </li>
                                @if($specialist->user->phone)
                                    <li class="list-group-item">
                                        <b>Телефон</b> <a class="float-right">{{$specialist->user->phone}}</a>
                                    </li>
                                @endif
                                @if($specialist->user->gender)
                                    <li class="list-group-item">
                                        <b>Пол</b> <a
                                            class="float-right">{{$specialist->user->gender=='male'? 'Мужской': 'Женский'}}</a>
                                    </li>
                                @endif
                                @if($specialist->user->b_day)
                                    <li class="list-group-item">
                                        <b>Дата рождения</b> <a class="float-right">{{$specialist->user->b_day}}</a>
                                    </li>
                                @endif
                                @if($specialist->user->url)
                                    <li class="list-group-item">
                                        <b>URL</b> <a href="{{$specialist->user->url}}" target="_blank"
                                                      class="float-right">{{$specialist->user->url}}</a>
                                    </li>
                                @endif
                                @if($specialist->user->verified_at)
                                    <li class="list-group-item text-sm">
                                        <b>Зарегистрирован</b> <a
                                            class="float-right">{{$specialist->user->verified_at}}</a>
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

                    @empty(!$specialist->user->location)
                        <!-- /.card-header -->
                            <div class="card-body">
                                <strong><i class="fas fa-map-marker-alt mr-1"></i>Расположение</strong>
                                <p class="text-muted">
                                    {{$specialist->user->location->city}}
                                </p>
                            </div>
                            <!-- /.card-body -->
                        @endempty
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#education"
                                                        data-toggle="tab">Образование</a>
                                <li class="nav-item"><a class="nav-link" href="#bio"
                                                        data-toggle="tab">Биография</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#schedules"
                                                        data-toggle="tab">Расписание</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#video" data-toggle="tab">Видео</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#chat" data-toggle="tab">Чаты</a>
                                </li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="education">
                                    <div class="text-left">
                                        <!-- /.card-header -->
                                        <table id="data_table" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Уровень</th>
                                                <th>Учебное заведение</th>
                                                <th>Факультет</th>
                                                <th>Специальность</th>
                                                <th>Год окончания</th>
                                                <th>Статус</th>
                                                <th>Documents</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($specialist->educations as $education)
                                                    <?php $diplomas = $education->getMedia('diploma') ?>
                                                <tr>
                                                    <td>{{$education->level}}</td>
                                                    <td>{{$education->institution}}</td>
                                                    <td>{{$education->faculty}}</td>
                                                    <td>{{$education->specialty}}</td>
                                                    <td>{{$education->graduation_at}}</td>
                                                    <td>{!! \App\Helper\Badge::getByStatusName($education->status)!!}</td>
                                                    <td>
                                                        <div class="row">
                                                            @foreach($diplomas as $diploma)
                                                                @if($diploma->mime_type == 'application/pdf')
                                                                    <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                                                                        <li>
                                                                    <span class="mailbox-attachment-icon"><i
                                                                            class="far fa-file-pdf"></i></span>

                                                                            <div class="mailbox-attachment-info">
                                                                                <a href="{{$diploma->getFullUrl()}}"
                                                                                   class="mailbox-attachment-name"><i
                                                                                        class="fas fa-paperclip"></i> {{$diploma->file_name}}
                                                                                </a>
                                                                                <span
                                                                                    class="mailbox-attachment-size clearfix mt-1">
                                                                      <span>{{ $diploma->human_readable_size}}</span>
                                                                      <a href="{{$diploma->getFullUrl()}}" download=""
                                                                         class="btn btn-default btn-sm float-right"><i
                                                                              class="fas fa-cloud-download-alt"></i></a>
                                                                    </span>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                @else
                                                                    <div class="col-sm-3">
                                                                        <a href="{{$diploma->getFullUrl()}}"
                                                                           data-toggle="lightbox"
                                                                           data-gallery="gallery">
                                                                            <img src="{{$diploma->getFullUrl()}}"
                                                                                 class="img-fluid mb-2"
                                                                                 alt="white sample"/>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if($education->status == Status::PENDING || $education->status == Status::FOR_CHECKING)
                                                                <form id="education_active_{{$education->id}}"
                                                                      action="{{url("/education/{$education->id}/active")}}"
                                                                      method="post">
                                                                    @csrf
                                                                </form>
                                                                <a href="javascript:void(0)"
                                                                   onclick="$('#education_active_{{$education->id}}').submit()"
                                                                   title="Активировать"
                                                                   class="btn btn-success">
                                                                    <i class="fa fa-check"></i></a>
                                                            @elseif($education->status == Status::ACTIVE)
                                                                <form id="education_pending_{{$education->id}}"
                                                                      action="{{url("/education/{$education->id}/pending")}}"
                                                                      method="post">
                                                                    @csrf
                                                                </form>
                                                                <a href="javascript:void(0)"
                                                                   onclick="$('#education_pending_{{$education->id}}').submit()"
                                                                   title="Добавить в ожидание"
                                                                   class="btn btn-warning">
                                                                    <i class="fa fa-ban"></i></a>
                                                            @endif
                                                            <button type="submit"
                                                                    data-action="{{url("/education/{$education->id}")}}"
                                                                    data-toggle="modal"
                                                                    title="Удалить"
                                                                    data-target="#modal-danger"
                                                                    class="btn btn-danger delete-item"><i
                                                                    class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>

                                        <!-- /.card-body -->
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="bio">
                                    <div class="text-left">
                                        @if($specialist->user->content)
                                            {!! $specialist->user->content !!}
                                        @else
                                            (Без содержания)
                                        @endif
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="active tab-pane" id="schedules">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-primary">
                                                <div class="card-body p-0">
                                                    <!-- THE CALENDAR -->
                                                    <div id="calendar"></div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="video">
                                    <div class="text-left">
                                        <?php $videos = $specialist->getMedia('video') ?>
                                        @if(!$videos->isEmpty())
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($videos as $video)
                                                        <div class="col-sm-12 ">
                                                            <video class="w-100" controls
                                                                   src="{{$video->getFullUrl()}}"></video>
                                                            <div class="btn-group btn-group-sm">
                                                                @if($video->getCustomProperty('status') == Status::ACTIVE)
                                                                    <form id="video_pending_{{$video->id}}"
                                                                          action="{{url("/specialist/video/{$video->id}/pending")}}"
                                                                          method="post">
                                                                        @csrf
                                                                    </form>
                                                                    <a href="javascript:void(0)"
                                                                       onclick="$('#video_pending_{{$video->id}}').submit()"
                                                                       title="Добавить в ожидание"
                                                                       class="btn btn-warning">Добавить в ожидание</a>
                                                                @else
                                                                    <form id="video_active_{{$video->id}}"
                                                                          action="{{url("/specialist/video/{$video->id}/active")}}"
                                                                          method="post">
                                                                        @csrf
                                                                    </form>
                                                                    <a href="javascript:void(0)"
                                                                       onclick="$('#video_active_{{$video->id}}').submit()"
                                                                       title="Активировать"
                                                                       class="btn btn-success">Активировать</a>
                                                                @endif
                                                                <button type="submit"
                                                                        data-action="{{url("/specialist/{$specialist->id}/video")}}"
                                                                        data-toggle="modal"
                                                                        title="Удалить"
                                                                        data-target="#modal-danger"
                                                                        class="btn btn-danger delete-item mx-1"><i
                                                                        class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                            <p class="text-center">
                                                                <span
                                                                    class="text-center">{{$diploma->human_readable_size}}</span>
                                                                <br/>
                                                                <span class="text-center">{{$video->file_name}}</span>
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif($specialist->video)
                                            <?php $url = $specialist->video?>
                                            <div class="card-body">
                                                <div class="row">
                                                    @if(strpos($url, 'youtube') > 0 || strpos($url, 'youtu.be'))
                                                        <div class="col-sm-12 h-100">
                                                            <iframe id="player" type="text/html" width="640"
                                                                    height="360"
                                                                    src="{{$url}}"
                                                                    frameborder="0">
                                                            </iframe>
                                                        </div>
                                                    @elseif(strpos($url, 'vimeo') > 0)
                                                        <iframe src="{{$url}}" width="640" height="360" frameborder="0"
                                                                webkitallowfullscreen mozallowfullscreen
                                                                allowfullscreen>
                                                        </iframe>
                                                    @else
                                                        <div class="col-sm-12 ">
                                                            <video class="w-100" controls
                                                                   src="{{$url}}"></video>
                                                        </div>
                                                    @endif
                                                    @if($specialist->video_status == Status::ACTIVE)
                                                        <form id="video_pending_{{$specialist->id}}"
                                                              action="{{url("/specialist/{$specialist->id}/video/pending")}}"
                                                              method="post">
                                                            @csrf
                                                        </form>
                                                        <a href="javascript:void(0)"
                                                           onclick="$('#video_pending_{{$specialist->id}}').submit()"
                                                           title="Добавить в ожидание"
                                                           class="btn btn-warning">Добавить в ожидание</a>
                                                    @else
                                                        <form id="video_active_{{$specialist->id}}"
                                                              action="{{url("/specialist/{$specialist->id}/video/active")}}"
                                                              method="post">
                                                            @csrf
                                                        </form>
                                                        <a href="javascript:void(0)"
                                                           onclick="$('#video_active_{{$specialist->id}}').submit()"
                                                           title="Активировать"
                                                           class="btn btn-success">Активировать</a>
                                                    @endif
                                                    <button type="submit"
                                                            data-action="{{url("/specialist/{$specialist->id}/video")}}"
                                                            data-toggle="modal"
                                                            title="Удалить"
                                                            data-target="#modal-danger"
                                                            class="btn btn-danger delete-item mx-1"><i
                                                            class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            {{'Без содержания'}}
                                        @endif
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="chat">
                                    <div class="text-left">
                                        <!-- DIRECT CHAT -->
                                        <div class="card direct-chat direct-chat-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Прямой чат</h3>

                                                <div class="card-tools">
                                                    <span title="{{$message_count}} Сообщения"
                                                          class="badge badge-primary"
                                                          id="message_count">{{$message_count}}</span>
                                                    <button type="button" class="btn btn-tool"
                                                            data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <button id="chat-pane-toggle" type="button" class="btn btn-tool"
                                                            title="Contacts"
                                                            data-widget="chat-pane-toggle">
                                                        <i class="fas fa-comments"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body" id="section-chat-messages" style="height: 500px;">
                                                <!-- Conversations are loaded here -->
                                                <div class="direct-chat-messages h-100"
                                                     id="direct-chat-messages">
                                                    <div id="direct-chat-message-list">
                                                        Пока нет сообщения
                                                    </div>
                                                    <div class="overlay" style="display: none;">
                                                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                                                    </div>
                                                </div>
                                                <!--/.direct-chat-messages-->

                                                <!-- Contacts are loaded here -->
                                                <div class="direct-chat-contacts h-100">
                                                    <ul class="contacts-list">
                                                        @if(!empty($users))
                                                            @foreach($users as $us)
                                                                <li>
                                                                    <a href="#"
                                                                       onclick="viewChat({{$specialist->user_id}},{{$us->id}})">
                                                                        @if(!empty($us->avatar_url))
                                                                            <img class="contacts-list-img"
                                                                                 src="{{$us->avatar_url}}"
                                                                                 alt="{{$us->full_name}}">
                                                                        @endif
                                                                        @if(!empty($us->last_message))
                                                                            <div class="contacts-list-info">
                                                                                <span class="contacts-list-name">
                                                                                    {{$us->full_name}}
                                                                                    <small class="contacts-list-date float-right">{{date('d-m-Y',strtotime($us->last_message->created_at))}}</small>
                                                                                </span>
                                                                                <span class="contacts-list-msg">{{$us->last_message->message}}</span>
                                                                            </div>
                                                                        @endif
                                                                    <!-- /.contacts-list-info -->
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            <li>Пока никого нет</li>
                                                    @endif
                                                    <!-- End Contact Item -->
                                                    </ul>
                                                    <!-- /.contacts-list -->
                                                </div>
                                                <!-- /.direct-chat-pane -->
                                            </div>
                                            <!-- /.card-body -->
                                            <!-- /.card-footer-->
                                        </div>
                                        <!--/.direct-chat -->
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
    @include('dashboard/partials/css/dataTable')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    <script src="{{asset("assets/lang/fullcalendar.ru.js")}}"></script>
    <script>
        $(function () {
            var Calendar = FullCalendar.Calendar;

            var calendarEl = document.getElementById('calendar');

            var calendar = new Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 650,
                contentHeight: 600,
                themeSystem: 'bootstrap',
                locale: 'ru',
                events: [],
                editable: false
            });

            calendar.render();
            $('#schedules').removeClass('active')

            $(document).on('click', '[data-toggle="lightbox"]', function (event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });

            let hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        })
    </script>
    @include('dashboard/partials/js/chatScrollLoader')
    @include('dashboard/partials/js/dataTable')
@endsection
