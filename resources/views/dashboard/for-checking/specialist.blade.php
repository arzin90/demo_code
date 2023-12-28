<?php

use App\Constants\Status;

?>

@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список специалистов для проверки</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table" class="table table-bordered table-striped">
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
                @foreach($specialists as $specialist)
                    <tr>
                        <td>{{$specialist->user->first_name}}</td>
                        <td>{{$specialist->user->last_name}}</td>
                        <td>{{$specialist->user->patronymic_name}}</td>
                        <td>{{$specialist->user->email}}</td>
                        <td>{{$specialist->user->phone}}</td>
                        <td>{!! \App\Helper\Badge::getByStatusName($specialist->status)!!}</td>
                        <td>{{$specialist->created_at}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{$url = url("/specialist/{$specialist->id}")}}" class="btn btn-info">
                                    <i class="fa fa-eye"></i></a>
                                @if($specialist->status == Status::PENDING || $specialist->status == Status::FOR_CHECKING)
                                    <form id="specialist_active_{{$specialist->id}}"
                                          action="{{url("/specialist/{$specialist->id}/active")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)"
                                       onclick="$('#specialist_active_{{$specialist->id}}').submit()"
                                       title="Активировать"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                @else
                                    <form id="specialist_pending_{{$specialist->id}}"
                                          action="{{url("/specialist/{$specialist->id}/pending")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)"
                                       onclick="$('#specialist_pending_{{$specialist->id}}').submit()"
                                       title="Добавить в ожидание"
                                       class="btn btn-warning">
                                        <i class="fa fa-ban"></i></a>
                                @endif
                                <button type="submit" data-action="{{$url}}" data-toggle="modal"
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
    @include('dashboard/partials/css/dataTable')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    @include('dashboard/partials/js/dataTable')
@endsection
