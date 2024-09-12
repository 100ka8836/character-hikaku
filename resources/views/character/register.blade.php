@extends('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('resources/css/style.css') }}">

<h1>キャラシのURL↓</h1>

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('register.character') }}" method="POST">
    @csrf
    <label for="url">URL:</label>
    <input type="url" name="url" id="url" required>
    <button type="submit">登録する</button>
</form>
@endsection