@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список Формат программы</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <a href="{{route('web-program-chapter.chapter.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Добавлять</a>
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Создатель</th>
                    <th>Имя</th>
                    <th>Тип</th>
                    <th>Статус</th>
                    <th>Создан</th>
                    <th>Обновлено</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapters as $item)
                    <tr>
                        @if($item->specialist)
                            <td><a href="{{url("/specialist/{$item->specialist->id}")}}"
                                   target="_blank">{{$item->specialist->user->full_name}}</a></td>
                        @else
                            <td>Админ</td>
                        @endif
                        <td>{{$item->name}}</td>
                        <td>{{ $item->type??'оба' }}</td>
                        <td>{!! \App\Helper\Badge::getByStatusName($item->status)!!}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{url("/chapter/{$item->id}/edit")}}" title="Редактировать"
                                   class="btn btn-primary">
                                    <i class="fa fa-edit"></i></a>
                                @if($item->status == \App\Constants\Status::PENDING)
                                    <form id="chapter_active_{{$item->id}}"
                                          action="{{url("/chapter/{$item->id}/active")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#chapter_active_{{$item->id}}').submit()"
                                       title="Активировать"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                @else
                                    <form id="chapter_pending_{{$item->id}}"
                                          action="{{url("/chapter/{$item->id}/pending")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#chapter_pending_{{$item->id}}').submit()"
                                       title="Добавить в ожидание"
                                       class="btn btn-warning">
                                        <i class="fa fa-ban"></i></a>
                                @endif
                                <button type="submit" data-action="{{url("/chapter/{$item->id}")}}"
                                        data-toggle="modal"
                                        title="Удалить"
                                        data-target="#modal-danger" class="btn btn-danger delete-item"><i
                                        class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
@endsection

@section('styles')
    @include('dashboard/partials/css/dataTable')
@endsection

@section('scripts')
    @include('dashboard/partials/js/dataTable')
@endsection
