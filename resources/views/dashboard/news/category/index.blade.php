@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список категорий</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <a href="{{route('web-news-category.category.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Добавлять</a>
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{url("/category/{$item->id}/edit")}}" title="Редактировать"
                                   class="btn btn-primary">
                                    <i class="fa fa-edit"></i></a>
                                <button type="submit" data-action="{{url("/category/{$item->id}")}}" data-toggle="modal"
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
