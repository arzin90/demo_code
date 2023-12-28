@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список всех пользователей</h3>
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
                @foreach($users as $user)
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
                                <a href="{{$url = url("/user/{$user->id}")}}" class="btn btn-info">
                                    <i class="fa fa-eye"></i></a>
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
                    select.append('<option value="В ожидании">В ожидании</option>')
                    select.append(`<option value="Заблокировано">Заблокировано</option>`)
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
