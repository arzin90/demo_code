<?php
use App\Constants\Status;
?>

@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список всех специалистов</h3>
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
                                    <form id="specialist_active_{{$specialist->id}}" action="{{url("/specialist/{$specialist->id}/active")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#specialist_active_{{$specialist->id}}').submit()"
                                       title="Активировать"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                @else
                                    <form id="specialist_pending_{{$specialist->id}}" action="{{url("/specialist/{$specialist->id}/pending")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#specialist_pending_{{$specialist->id}}').submit()"
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
    @include('dashboard/partials/css/dataTable')
@endsection

@section('scripts')
    @include('dashboard/partials/js/dataTable')
    <script>
        $(document).ready(function () {
            let table = $('#data_table').DataTable();

            $("#data_table thead th").clone(true)
                .addClass('filters')
                .appendTo('#data_table thead').each(function (i) {
                if (i === 5) {
                    let select = $('<select><option value="">Все</option></select>')
                    select.append('<option value="Активный">Активный</option>')
                    select.append('<option value="На проверку">На проверку</option>')
                    select.append(`<option value="Заблокировано">Заблокировано</option>`)
                    select.append(`<option value="Удалено">Удалено</option>`)
                        .appendTo($(this).empty())
                        .on('change', function () {
                            let val = $(this).val();

                            table.column(i)
                                .search(val ? $(this).val() : val, true, false)
                                .draw();
                        });
                } else if (i !== 7) {
                    $(this).html('<input class="col-12" type="text" value="">');

                    $('th.filters').eq(i).find('input')
                        .off('keyup change')
                        .on('keyup change', function (e) {
                            e.stopPropagation();

                            // Get the search value
                            $(this).attr('title', $(this).val());
                            let regexr = '({search})'; //$(this).parents('th').find('select').val();

                            // Search the column for that value
                            let val = $(this).val();
                            table.column(i)
                                .search(
                                    val ? regexr.replace('{search}', '(((' + val + ')))') : '',
                                    true,
                                    false
                                ).draw();
                        });
                }
            });
        });
    </script>
@endsection
