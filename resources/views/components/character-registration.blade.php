<div class="character-registration">
    <h2>キャラシのURL↓</h2>

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
</div>