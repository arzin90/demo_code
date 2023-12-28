<?php
use App\Constants\Status;
?>

@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список видео для проверки</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table_2" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Отчество</th>
                    <th>Эл. почта</th>
                    <th>Телефон</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($videos as $video)
                    <tr>
                        <td>{{$video->user->first_name}}</td>
                        <td>{{$video->user->last_name}}</td>
                        <td>{{$video->user->patronymic_name}}</td>
                        <td>{{$video->user->email}}</td>
                        <td>{{$video->user->phone}}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{$url = url("/specialist/{$video->id}#video")}}" class="btn btn-info">
                                    <i class="fa fa-eye"></i>
                                </a>
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
