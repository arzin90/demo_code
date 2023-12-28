<?php

use App\Constants\Status;

?>

@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список жалоб для программ</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Программа</th>
                    <th>Пользовател</th>
                    <th>Текст</th>
                    <th>Статус</th>
                    <th>Создан</th>
                    <th>Обновлено</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($program_complaints as $item)
                    <tr>
                        <td>{{$item->program?$item->program->name:'Не указано'}}</td>
                        <td>{{!empty($item->user)?$item->user->full_name:'Не указано'}}</td>
                        <td>{{$item->message}}</td>
                        <td>{!! \App\Helper\Badge::getByStatusName($item->status)!!}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{url("/program/{$item->program->id}")}}" title="Посмотреть програму подробнее"
                                   class="btn btn-info">
                                    <i class="fa fa-eye"></i></a>

                                @if($item->program->status == Status::PENDING || $item->program->status == Status::FOR_CHECKING)
                                    <form id="program_active_{{$item->program->id}}"
                                          action="{{url("/program/{$item->program->id}/active")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#program_active_{{$item->program->id}}').submit()"
                                       title="Активировать програму"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                    <button type="submit" data-action="{{url("/program/{$item->program->id}")}}"
                                            data-toggle="modal"
                                            title="Удалить програму"
                                            data-target="#modal-danger" class="btn btn-danger delete-item"><i
                                            class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <form id="program_pending_{{$item->program->id}}"
                                          action="{{url("/program/{$item->program->id}/pending")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)"
                                       onclick="$('#program_pending_{{$item->program->id}}').submit()"
                                       title="Добавить програму в ожидание"
                                       class="btn btn-warning">
                                        <i class="fa fa-ban"></i></a>
                                @endif
                                <button type="submit" data-action="{{url("/program-complaint/{$item->id}")}}"
                                        data-toggle="modal"
                                        title="Удалить жалобу"
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
    <script>
        $(document).ready(function () {
            let table = $('#data_table').DataTable();
        });
    </script>
@endsection
