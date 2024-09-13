<!-- resources/views/components/character-registration.blade.php -->
<div class="character-registration">
    <h2>キャラシのURLを入力してください</h2>

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

        <!-- キャラクタータイプを指定する hidden フィールド -->
        <input type="hidden" name="type" value="{{ $type }}">

        <button type="submit">登録する</button>
    </form>
</div>