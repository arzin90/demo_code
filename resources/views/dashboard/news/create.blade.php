@extends('layouts.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Добавить новости</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('web-news.news.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Заголовок</label>
                                <input type="text" id="title" name="title" value="{{old('title')}}"
                                       class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="short_description">Краткое описание</label>
                                <textarea id="short_description"
                                          class="form-control @error('short_description') is-invalid @enderror"
                                          name="short_description" rows="4">{{old('short_description')}}</textarea>
                                @error('short_description')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Описание</label>
                                <textarea id="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          rows="4">{{old('description')}}</textarea>
                                @error('description')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category">Категория</label>
                                <select id="category" name="category"
                                        class="form-control custom-select @error('category') is-invalid @enderror">
                                    @foreach($categories as $category)
                                        <option
                                            value="{{$category->id}}" {{old('category') && old('category')==$category->id?'selected':''}}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="location_id">Место расположения</label>
                                <select id="location_id" name="location_id"
                                        class="form-control custom-select @error('location_id') is-invalid @enderror">
                                    <option value="">Выбрать...</option>
                                    @foreach($locations as $location)
                                        <option
                                            value="{{$location->id}}" {{old('location_id') && old('location_id')==$location->city?'selected':''}}>{{$location->city}}</option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="image">Изображение</label>
                                <div class="input-group @error('image') is-invalid @enderror">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" id="image">
                                        <label class="custom-file-label" for="image">Выбрать файл</label>
                                    </div>
                                </div>
                                @error('image')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="status_id">Статус</label>
                                <select id="status_id" name="status_id"
                                        class="form-control custom-select @error('status_id') is-invalid @enderror">
                                    @foreach($statusList as $key => $status)
                                        <option
                                            value="{{$key}}" {{old('status') && old('status')==$key?'selected':''}}>{{$status}}</option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success form-control">Создать</button>
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
