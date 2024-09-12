@extends('layout')

@section('content')
<h1>キャラクター編集</h1>

<form action="{{ route('characters.update', $character->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label for="name">名前:</label>
    <input type="text" name="name" id="name" value="{{ $character->name }}">

    <label for="occupation">職業:</label>
    <input type="text" name="occupation" id="occupation" value="{{ $character->occupation }}">

    <label for="age">年齢:</label>
    <input type="text" name="age" id="age" value="{{ $character->age }}">

    <br>

    <label for="sex">性別:</label>
    <input type="text" name="sex" id="sex" value="{{ $character->sex }}">

    <label for="birthplace">出身:</label>
    <input type="text" name="birthplace" id="birthplace" value="{{ $character->birthplace }}">

    <label for="degree">学位:</label>
    <input type="text" name="degree" id="degree" value="{{ $character->degree }}">

    <!-- 能力値編集 -->
    <h4>能力値</h4>
    <label for="str">STR:</label>
    <input type="text" name="abilities[str]" id="str" value="{{ $character->abilities['str'] ?? '' }}">

    <label for="con">CON:</label>
    <input type="text" name="abilities[con]" id="con" value="{{ $character->abilities['con'] ?? '' }}">

    <label for="pow">POW:</label>
    <input type="text" name="abilities[pow]" id="pow" value="{{ $character->abilities['pow'] ?? '' }}">

    <label for="dex">DEX:</label>
    <input type="text" name="abilities[dex]" id="dex" value="{{ $character->abilities['dex'] ?? '' }}">

    <br>

    <label for="app">APP:</label>
    <input type="text" name="abilities[app]" id="app" value="{{ $character->abilities['app'] ?? '' }}">

    <label for="siz">SIZ:</label>
    <input type="text" name="abilities[siz]" id="siz" value="{{ $character->abilities['siz'] ?? '' }}">

    <label for="int">INT:</label>
    <input type="text" name="abilities[int]" id="int" value="{{ $character->abilities['int'] ?? '' }}">

    <label for="edu">EDU:</label>
    <input type="text" name="abilities[edu]" id="edu" value="{{ $character->abilities['edu'] ?? '' }}">

    <br>

    <label for="hp">HP:</label>
    <input type="text" name="abilities[hp]" id="hp" value="{{ $character->abilities['hp'] ?? '' }}">

    <label for="mp">MP:</label>
    <input type="text" name="abilities[mp]" id="mp" value="{{ $character->abilities['mp'] ?? '' }}">

    <label for="db">DB:</label>
    <input type="text" name="abilities[db]" id="db" value="{{ $character->abilities['db'] ?? '' }}">

    <br>

    <label for="san_value">現在SAN:</label>
    <input type="text" name="abilities[san_value]" id="san_value"
        value="{{ $character->abilities['san_value'] ?? '' }}">

    <label for="san_max">最大SAN:</label>
    <input type="text" name="abilities[san_max]" id="san_max" value="{{ $character->abilities['san_max'] ?? '' }}">

    <!-- 技能編集 -->
    <h4>技能</h4>
    @foreach($character->skills as $index => $skill)
        <div class="form-group">
            <label for="skills_{{ $index }}_name"></label>
            <input type="text" name="skills[{{ $index }}][name]" value="{{ $skill['name'] ?? '' }}" class="form-control">

            <label for="skills_{{ $index }}_value">:</label>
            <input type="number" name="skills[{{ $index }}][value]" value="{{ $skill['value'] ?? 0 }}" class="form-control">
        </div>
    @endforeach

    <button type="submit">更新する</button>
</form>
@endsection