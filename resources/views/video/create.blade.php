@extends('layouts.app')
@section('style')
    <style>
        main {
            background-image: url('{{ asset('storage/images/1.png') }}');
        }
    </style>
@endsection
@section('content')
<div class="container" >
    <div class="row">
        <h2 style="color: aliceblue;">Crear un nuevo video</h2>
        <hr>
        <form action="{{route('videos.store')}}" method="post" enctype="multipart/form-data" class="col-lg-7">
            {!! csrf_field() !!} <!-- Protección contra ataques ya implementado en laravel  https://www.welivesecurity.com/la-es/2015/04/21/vulnerabilidad-cross-site-request-forgery-csrf/-->
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label for="title" style="color: aliceblue;" >Título</label>
                <input type="text" class="form-control" id="title" name="title" value="{{old('title')}}" />
            </div>
            <div class="form-group">
                <label for="description" style="color: aliceblue;">Descripción</label>
                <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
            </div>
            <div class="form-group">
                <label for="image" style="color: aliceblue;">Miniaturas</label>
                <input type="file" class="form-control" id="image" name="image" />
            </div>
            <div class="form-group">
                <label for="video" style="color: aliceblue;">Archivo de Vídeo</label>
                <input type="file" class="form-control" id="video" name="video" />
            </div>
            <br>
            <button type="submit" class="btn btn-success">Crear Vídeo</button>
            <br>
            <h1>.</h1>
            <h1>.</h1>
        </form>
    </div>
</div>
@endsection