<?php

use App\Constants\Status;

?>

@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список программ</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Ведущий программы</th>
                    <th>Специалист</th>
                    <th>Категория</th>
{{--                    <th>Количество участников</th>--}}
                    <th>Цена</th>
                    <th>Расположение</th>
                    <th>Статус</th>
                    <th>Создан</th>
                    <th>Обновлено</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($programs as $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->presenter?: (!empty($item->presenter_user) && $item->presenter_user->full_name ? $item->presenter_user->full_name : 'Не указано')}}</td>
                        <td>{{!empty($item->specialist)?$item->specialist->user->full_name:'Не указано'}}</td>
                        <td>{{!empty($item->categories)?$item->categories->pluck('name')->join(', '):'Не указано'}}</td>
{{--                        <td>{{$item->member_count}}</td>--}}
                        <td>{{$item->price}}</td>
                        <td>{{!empty($item->location)?$item->location->city:'Не указано'}}</td>
                        <td>{!! \App\Helper\Badge::getByStatusName($item->status)!!}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{url("/program/{$item->id}")}}" title="Посмотреть подробнее"
                                   class="btn btn-info">
                                    <i class="fa fa-eye"></i></a>
                                @if($item->status == Status::PENDING || $item->status == Status::FOR_CHECKING)
                                    <form id="program_active_{{$item->id}}"
                                          action="{{url("/program/{$item->id}/active")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#program_active_{{$item->id}}').submit()"
                                       title="Активировать"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                    <button type="submit" data-action="{{url("/program/{$item->id}")}}"
                                            data-toggle="modal"
                                            title="Удалить"
                                            data-target="#modal-danger" class="btn btn-danger delete-item"><i
                                            class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <form id="program_pending_{{$item->id}}"
                                          action="{{url("/program/{$item->id}/pending")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)"
                                       onclick="$('#program_pending_{{$item->id}}').submit()"
                                       title="Добавить в ожидание"
                                       class="btn btn-warning">
                                        <i class="fa fa-ban"></i></a>
                                @endif
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
            // Read a page's GET URL variables and return them as an associative array.
            function getUrlVars()
            {
                var vars = [], hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for(var i = 0; i < hashes.length; i++)
                {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;
            }

            function updateQueryString(key, value) {
                if (history.pushState) {
                    let searchParams = new URLSearchParams(window.location.search);
                    searchParams.set(key, value);
                    let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
                    window.history.pushState({path: newurl}, '', newurl);
                }
            }

            let table = $('#data_table').DataTable();

            $("#data_table thead th").clone(true)
                .addClass('filters')
                .appendTo('#data_table thead').each(function (i) {
                if (i === 6) {
                    let selected = getUrlVars()['status']??''
                    selected = selected? decodeURIComponent(selected) : '';
                    let select = $(`<select><option value="" ${ selected === '' ? 'selected' : ''}>Все</option></select>`)
                    select.append(`<option value="Активный" ${ selected === 'Активный' ? 'selected' : ''}>Активный</option>`)
                    select.append(`<option value="На проверку" ${ selected === 'На+проверку' ? 'selected' : ''}>На проверку</option>`)
                    select.append(`<option value="Деактивирован" ${ selected === 'Деактивирован' ? 'selected' : ''}>Деактивирован</option>`)
                    select.append(`<option value="Заблокировано" ${ selected === 'Заблокировано' ? 'selected' : ''}>Заблокировано</option>`)
                        .appendTo($(this).empty())
                        .on('change', function () {
                            let val = $(this).val().trim();
                            updateQueryString('status', val)

                            table.column(i)
                                .search(val ? $(this).val() : val, true, false)
                                .draw();
                        });
                } else if (i !== 9) {
                    $(this).html('<input class="col-12" type="text" value="">');

                    $('th.filters').eq(i).find('input')
                        .off('keyup change')
                        .on('keyup change', function (e) {
                            e.stopPropagation();

                            // Get the search value
                            $(this).attr('title', $(this).val());
                            let regexr = '({search})'; //$(this).parents('th').find('select').val();

                            // Search the column for that value
                            let val = $(this).val().trim();
                            table.column(i)
                                .search(
                                    val ? regexr.replace('{search}', '(((' + val + ')))') : '',
                                    true,
                                    false
                                ).draw();
                        });
                }
            });

            $('#data_table select').change()
        });
    </script>
@endsection
