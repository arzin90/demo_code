<?php
?>

@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список образований для проверки</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data_table_1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Уровень</th>
                    <th>Специалист</th>
                    <th>Учебное заведение</th>
                    <th>Факультет</th>
                    <th>Специальность</th>
                    <th>Год окончания</th>
                    <th>Статус</th>
                    <th>Documents</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($educations as $education)
                    <?php $diplomas = $education->getMedia('diploma'); ?>
                    <tr>
                        <td>{{$education->level}}</td>
                        <td>{{$education->specialist->user->fullName}}</td>
                        <td>{{$education->institution}}</td>
                        <td>{{$education->faculty}}</td>
                        <td>{{$education->specialty}}</td>
                        <td>{{$education->graduation_at}}</td>
                        <td>{!! \App\Helper\Badge::getByStatusName($education->status)!!}</td>
                        <td>
                            <div class="row">
                                @foreach($diplomas as $diploma)
                                    @if($diploma->mime_type == 'application/pdf')
                                        <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                                            <li>
                                                <span class="mailbox-attachment-icon">
                                                    <i class="far fa-file-pdf"></i>
                                                </span>

                                                <div class="mailbox-attachment-info">
                                                    <a href="{{$diploma->getFullUrl()}}"
                                                       class="mailbox-attachment-name">
                                                        <i class="fas fa-paperclip"></i> {{$diploma->file_name}}
                                                    </a>
                                                    <span class="mailbox-attachment-size clearfix mt-1">
                                                          <span>{{ $diploma->human_readable_size}}</span>
                                                          <a href="{{$diploma->getFullUrl()}}" download=""
                                                             class="btn btn-default btn-sm float-right">
                                                              <i class="fas fa-cloud-download-alt"></i>
                                                          </a>
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    @else
                                        <div class="col-sm-3">
                                            <a href="{{$diploma->getFullUrl()}}"
                                               data-toggle="lightbox"
                                               data-gallery="gallery">
                                                <img src="{{$diploma->getFullUrl()}}"
                                                     class="img-fluid mb-2"
                                                     alt="white sample"/>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{$url = url("/specialist/{$education->specialist->id}")}}"
                                   class="btn btn-info">
                                    <i class="fa fa-eye"></i></a>
                                @if($education->status == Status::PENDING || $education->status == Status::FOR_CHECKING)
                                    <form id="education_active_{{$education->id}}"
                                          action="{{url("/education/{$education->id}/active")}}"
                                          method="post">
                                        @csrf
                                    </form>
                                    <a href="javascript:void(0)"
                                       onclick="$('#education_active_{{$education->id}}').submit()"
                                       title="Активировать"
                                       class="btn btn-success">
                                        <i class="fa fa-check"></i></a>
                                @endif
                                <button type="submit"
                                        data-action="{{url("/education/{$education->id}")}}"
                                        data-toggle="modal"
                                        title="Удалить"
                                        data-target="#modal-danger"
                                        class="btn btn-danger delete-item"><i
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
