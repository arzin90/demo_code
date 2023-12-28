@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список специальности</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table_location" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Город</th>
                    <th>Федеральный район</th>
                    <th>Область</th>
                    <th>Почтовый индекс</th>
                    <th>Часовой пояс</th>
                    <th>Популярный</th>
                </tr>
                </thead>
                <tbody>
                @foreach($locations as $item)
                    <tr>
                        <td>{{$item->city}}</td>
                        <td>{{$item->federal_district}}</td>
                        <td>{{$item->region}}</td>
                        <td>{{$item->postal_code}}</td>
                        <td>{{$item->timezone}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form id="location_popular_{{$item->id}}" action="{{url("/location/{$item->id}")}}"
                                      method="post">
                                    @method('PUT')
                                    @csrf
                                    <input type="number" min="0" name="popular" value="{{$item->popular}}">
                                </form>
                                <a href="javascript:void(0)" onclick="$('#location_popular_{{$item->id}}').submit()"
                                   class="btn btn-warning">Сохранить</a>
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
        $(function () {
            $("#data_table_location").DataTable({
                language: {
                    url:'{{asset('assets/lang/datatable.ru.json')}}'
                },
                "sort": false,
                "responsive": true, "lengthChange": false, "autoWidth": false
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
