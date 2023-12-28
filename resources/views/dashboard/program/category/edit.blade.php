@extends('layouts.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{!! sprintf('Редактировать категория программ <b>%s</b>', $programCategory->name) !!}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('web-program-category.program-category.update',['program_category'=>$programCategory->id])}}" method="post">
                        @method('put')
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" id="name" name="name" value="{{old('name')?:$programCategory->name}}"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <span class="error invalid-feedback">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="status">Статус</label>
                                <select id="status" name="status"
                                        class="form-control custom-select @error('status') is-invalid @enderror">
                                    @foreach($statusList as $key => $status)
                                        <option
                                            value="{{$key}}" {{old('status') && old('status')==$key || $programCategory->status==$key?'selected':''}}>{{$status}}</option>
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
