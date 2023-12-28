@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список всех новостей</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Заголовок</th>
                    <th>Краткое описание</th>
                    <th>Категория</th>
                    <th>Место расп.</th>
                    <th>Просмотры</th>
                    <th>Статус</th>
                    <th>Добавлен</th>
                    <th>Изменён</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($news as $item)
                    <tr>
                        <td>{{$item->title}}</td>
                        <td>{{$item->short_description}}</td>
                        <td>{{$item->categories[0]->name}}</td>
                        <td>{{$item->location?$item->location->city:'Не выбрано'}}</td>
                        <td>{{$item->view_count}}</td>
                        <td>{!! \App\Helper\Badge::getByStatusName($item->status_id)!!}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{url("/news/{$item->id}/edit")}}" title="Редактировать"
                                   class="btn btn-primary">
                                    <i class="fa fa-edit"></i></a>
                                @if($item->status_id == \App\Models\News::STATUS_PENDING)
                                    <form id="news_publish_{{$item->id}}" action="{{url("/news/{$item->id}/publish")}}" method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#news_publish_{{$item->id}}').submit()"
                                       title="Опубликовать"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                @else
                                    <form id="news_pending_{{$item->id}}" action="{{url("/news/{$item->id}/pending")}}" class=""
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)" onclick="$('#news_pending_{{$item->id}}').submit()"
                                       title="Добавить в ожидание"
                                       class="btn btn-warning">
                                        <i class="fa fa-ban"></i></a>
                                @endif
                                <button type="submit" data-action="{{url("/news/{$item->id}")}}" data-toggle="modal"
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
