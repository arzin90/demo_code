@extends('layouts.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{!! sprintf('Редактировать Формат программы <b>%s</b>', $chapter->name) !!}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('web-program-chapter.chapter.update',['chapter'=>$chapter->id])}}" method="post">
                        @method('put')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" id="name" name="name" value="{{old('name')?:$chapter->name}}"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="type">Тип</label>
                                <select id="type" name="type"
                                        class="form-control custom-select @error('type') is-invalid @enderror">
                                    <option value="">Оба</option>
                                    @foreach($typeList as $key => $type)
                                        <option
                                            value="{{$key}}" {{old('type') && old('type')==$key || $chapter->type==$key?'selected':''}}>{{$type}}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status">Статус</label>
                                <select id="status" name="status"
                                        class="form-control custom-select @error('status') is-invalid @enderror">
                                    @foreach($statusList as $key => $status)
                                        <option
                                            value="{{$key}}" {{old('status') && old('status')==$key || $chapter->status==$key?'selected':''}}>{{$status}}</option>
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
