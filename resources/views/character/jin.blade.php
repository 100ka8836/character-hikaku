@extends('layout')

@section('content')
<h1>陣ごとのキャラクター一覧</h1>

<form action="#" method="POST">
    @csrf
    <label for="jin_name">陣名:</label>
    <input type="text" name="jin_name" id="jin_name" required>

    <label for="character_paste">キャラクターデータペースト:</label>
    <textarea name="character_paste" id="character_paste" rows="5"></textarea>

    <button type="submit" class="btn btn-primary">陣を作成</button>
</form>

<hr>

<h2>自分のキャラクター</h2>
<ul>
    @foreach($selfCharacters as $character)
        <li>{{ $character->name }}</li>
    @endforeach
</ul>

<h2>友達のキャラクター</h2>
<ul>
    @foreach($friendCharacters as $character)
        <li>{{ $character->name }}</li>
    @endforeach
</ul>

@endsection