@extends('layouts.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{!! sprintf('Редактировать страницу <b>%s</b>', $page->title) !!}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('page.update', ['page' => $page->id])}}" method="post"
                          enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Заголовок</label>
                                <input type="text" id="title" name="title" value="{{old('title')?:$page->title}}"
                                       class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="key">Ключ</label>
                                <input type="text" id="key" name="key" value="{{old('key')?:$page->key}}"
                                       class="form-control @error('key') is-invalid @enderror">
                                @error('key')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Содержание</label>
                                <textarea id="description"
                                          class="form-control @error('content') is-invalid @enderror"
                                          name="content"
                                          rows="4">{!! old('content')?:$page->content !!}</textarea>
                                @error('content')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="status">Статус</label>
                                <select id="status" name="status"
                                        class="form-control custom-select @error('status') is-invalid @enderror">
                                    @foreach($statusList as $key => $status)
                                        <option
                                            value="{{$key}}" {{old('status') && old('status')== $key || $key == $page->status?'selected':''}}>{{$status}}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success form-control">Сохранить</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="{{asset('assets/lang/summernote.ru.js')}}"></script>
    <script>
        $(function () {
            // Summernote
            $('#description').summernote({
                lang: 'ru-RU',
                height: 250
            })

        })
    </script>
@endsection

