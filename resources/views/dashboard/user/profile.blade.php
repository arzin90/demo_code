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
                                     src="{{$user->avatar_url}}"
                                     alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">{{$user->full_name}}</h3>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Статус</b> <a
                                        class="float-right">{!! \App\Helper\Badge::getByStatusId($user->status_id) !!}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>E-Mail</b> <a class="float-right">{{$user->email}}</a>
                                </li>
                                @if($user->phone)
                                    <li class="list-group-item">
                                        <b>Телефон</b> <a class="float-right">{{$user->phone}}</a>
                                    </li>
                                @endif
                                @if($user->gender)
                                    <li class="list-group-item">
                                        <b>Пол</b> <a
                                            class="float-right">{{$user->gender=='male'? 'Мужской': 'Женский'}}</a>
                                    </li>
                                @endif
                                @if($user->b_day)
                                    <li class="list-group-item">
                                        <b>Дата рождения</b> <a class="float-right">{{$user->b_day}}</a>
                                    </li>
                                @endif
                                @if($user->url)
                                    <li class="list-group-item">
                                        <b>URL</b> <a href="{{$user->url}}" target="_blank"
                                                      class="float-right">{{$user->url}}</a>
                                    </li>
                                @endif
                                @if($user->verified_at)
                                    <li class="list-group-item text-sm">
                                        <b>Зарегистрирован</b> <a class="float-right">{{$user->verified_at}}</a>
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
                        <!-- /.card-header -->
                        <div class="card-body">
                            <strong><i class="fas fa-book mr-1"></i>Образование</strong>

                            <p class="text-muted">

                            </p>

                            <hr>

                            <strong><i class="fas fa-map-marker-alt mr-1"></i>Расположение</strong>

                            <p class="text-muted">

                            </p>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#bio"
                                                        data-toggle="tab">Биография</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#schedules"
                                                        data-toggle="tab">Расписание</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#doc" data-toggle="tab">Документы</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#chat" data-toggle="tab">Чаты</a>
                                </li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="bio">
                                    <div class="text-left">
                                        @if($user->content)
                                            {!! $user->content !!}
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

                                <div class="tab-pane" id="doc">
                                    <div class="text-left">
                                        (Без содержания)
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
                                                                       onclick="viewChat({{$user->id}},{{$us->id}})">
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
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.js"></script>
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
        })

    </script>

    @include('dashboard/partials/js/chatScrollLoader')
@endsection
