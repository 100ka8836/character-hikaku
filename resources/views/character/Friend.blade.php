@extends('layout')

@section('content')
<h1>友達のキャラクター一覧</h1>

@include('components.character-registration')

<hr>

<!-- 表示切替ボタン -->
<button id="toggle-basic-button" class="toggle-button">基本情報</button>
<button id="toggle-abilities-button" class="toggle-button">能力値</button>
<button id="toggle-skills-button" class="toggle-button">技能</button>

@if($characters->isEmpty())
    <p>まだキャラクターが登録されていません。</p>
@else
    <table class="character-table" id="characterTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">名前</th>
                <!-- 他のヘッダーカラム -->
            </tr>
        </thead>
        <tbody>
            @foreach($characters as $character)
                <tr>
                    <td>{{ $character->name }}</td>
                    <!-- 他のキャラクターデータ -->
                    <td>
                        <a href="{{ route('characters.edit', $character->id) }}" class="btn btn-primary">編集</a>
                        <form action="{{ route('characters.destroy', $character->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">削除</button>
                        </form>
                        <form action="{{ route('characters.copy', $character->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary">コピー</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection