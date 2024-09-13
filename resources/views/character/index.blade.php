@extends('layout')

@section('content')
<h1>{{ request()->is('characters/self') ? '自分のキャラクター' : '友達のキャラクター' }}</h1>

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
                <th class="toggle-basic" style="display:none;" onclick="sortTable(1)">職業</th>
                <th class="toggle-basic" style="display:none;" onclick="sortTable(2)">年齢</th>
                <th class="toggle-basic" style="display:none;" onclick="sortTable(3)">性別</th>
                <th class="toggle-basic" style="display:none;" onclick="sortTable(4)">出身</th>
                <th class="toggle-basic" style="display:none;" onclick="sortTable(5)">学位</th>
                <th class="toggle-abilities" onclick="sortTable(6)">STR</th>
                <th class="toggle-abilities" onclick="sortTable(7)">CON</th>
                <th class="toggle-abilities" onclick="sortTable(8)">POW</th>
                <th class="toggle-abilities" onclick="sortTable(9)">DEX</th>
                <th class="toggle-abilities" onclick="sortTable(10)">APP</th>
                <th class="toggle-abilities" onclick="sortTable(11)">SIZ</th>
                <th class="toggle-abilities" onclick="sortTable(12)">INT</th>
                <th class="toggle-abilities" onclick="sortTable(13)">EDU</th>
                <th class="toggle-abilities" onclick="sortTable(14)">HP</th>
                <th class="toggle-abilities" onclick="sortTable(15)">MP</th>
                <th class="toggle-abilities" onclick="sortTable(16)">DB</th>
                <th class="toggle-abilities" onclick="sortTable(17)">現在SAN</th>
                <th class="toggle-abilities" onclick="sortTable(18)">最大SAN</th>
                <th class="toggle-skills" style="display:none;">技能</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($characters as $character)
                <tr>
                    <td>{{ $character->name }}</td>
                    <td class="toggle-basic" style="display:none;">{{ $character->occupation }}</td>
                    <td class="toggle-basic" style="display:none;">{{ $character->age }}</td>
                    <td class="toggle-basic" style="display:none;">{{ $character->sex }}</td>
                    <td class="toggle-basic" style="display:none;">{{ $character->birthplace }}</td>
                    <td class="toggle-basic" style="display:none;">{{ $character->degree }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['str'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['con'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['pow'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['dex'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['app'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['siz'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['int'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['edu'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['hp'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['mp'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['db'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['san_value'] ?? '不明' }}</td>
                    <td class="toggle-abilities">{{ $character->abilities['san_max'] ?? '不明' }}</td>

                    <td class="toggle-skills" style="display:none;">
                        @if(!empty($character->skills))
                            @foreach($character->skills as $skill)
                                <span class="skill-item" data-name="{{ $skill['name'] }}"
                                    data-value="{{ $skill['value'] }}">{{ $skill['name'] }}: {{ $skill['value'] }}</span>
                            @endforeach
                        @else
                            <span class="skill-item">技能はありません。</span>
                        @endif
                    </td>

                    <!-- 編集、削除、コピー機能のボタン -->
                    <td>
                        <!-- 編集ボタン -->
                        <a href="{{ route('characters.edit', $character->id) }}" class="btn btn-primary">編集</a>

                        <!-- 削除ボタン -->
                        <form action="{{ route('characters.destroy', $character->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>

                        <!-- コピー機能ボタン -->
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

@push('scripts')
    <script>
        // ソート機能（属性用）
        function sortTable(n) {
            const table = document.getElementById("characterTable");
            let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            switching = true;
            dir = "asc"; // 初期ソート順は昇順

            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir === "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount === 0 && dir === "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }

        // ソート機能（技能用）
        function sortTableBySkill(skillName, direction) {
            const table = document.getElementById("characterTable");
            let rows = Array.from(table.rows).slice(1); // ヘッダー行は除外
            rows.sort(function (a, b) {
                let aValue = 0, bValue = 0;

                const aSkills = a.querySelectorAll('.skill-item');
                const bSkills = b.querySelectorAll('.skill-item');

                aSkills.forEach(skill => {
                    if (skill.dataset.name === skillName) {
                        aValue = parseInt(skill.dataset.value) || 0;
                    }
                });

                bSkills.forEach(skill => {
                    if (skill.dataset.name === skillName) {
                        bValue = parseInt(skill.dataset.value) || 0;
                    }
                });

                return (direction === 'asc') ? aValue - bValue : bValue - aValue;
            });

            rows.forEach(row => table.tBodies[0].appendChild(row)); // 並べ替えた行を再挿入
        }

        // 表示切替ボタンの機能
        document.addEventListener('DOMContentLoaded', function () {
            // 基本情報の表示/非表示を切り替える
            document.getElementById('toggle-basic-button').addEventListener('click', function () {
                const basicCols = document.querySelectorAll('.toggle-basic');
                const abilitiesCols = document.querySelectorAll('.toggle-abilities');
                const skillsCols = document.querySelectorAll('.toggle-skills');

                basicCols.forEach(col => col.style.display = (col.style.display === 'none') ? 'table-cell' : 'none');
                abilitiesCols.forEach(col => col.style.display = 'none');
                skillsCols.forEach(col => col.style.display = 'none');
            });

            // 能力値の表示/非表示を切り替える
            document.getElementById('toggle-abilities-button').addEventListener('click', function () {
                const abilitiesCols = document.querySelectorAll('.toggle-abilities');
                const basicCols = document.querySelectorAll('.toggle-basic');
                const skillsCols = document.querySelectorAll('.toggle-skills');

                abilitiesCols.forEach(col => col.style.display = (col.style.display === 'none') ? 'table-cell' : 'none');
                basicCols.forEach(col => col.style.display = 'none');
                skillsCols.forEach(col => col.style.display = 'none');
            });

            // 技能の表示/非表示を切り替える
            document.getElementById('toggle-skills-button').addEventListener('click', function () {
                const skillsCols = document.querySelectorAll('.toggle-skills');
                const basicCols = document.querySelectorAll('.toggle-basic');
                const abilitiesCols = document.querySelectorAll('.toggle-abilities');

                skillsCols.forEach(col => col.style.display = (col.style.display === 'none') ? 'table-cell' : 'none');
                basicCols.forEach(col => col.style.display = 'none');
                abilitiesCols.forEach(col => col.style.display = 'none');
            });

            // 技能項目のソート機能
            let currentDirection = 'asc'; // デフォルトのソート方向
            document.querySelectorAll('.skill-item').forEach(function (skillItem) {
                skillItem.addEventListener('click', function () {
                    const skillName = skillItem.dataset.name;
                    sortTableBySkill(skillName, currentDirection);

                    // 選択された技能に色を付ける（青）
                    document.querySelectorAll('.skill-item').forEach(function (el) {
                        el.classList.remove('active', 'highlight');
                        if (el.dataset.name === skillName) {
                            el.classList.add('highlight'); // 同じ技能を持つキャラクターに黄色
                        }
                    });
                    skillItem.classList.add('active'); // クリックされた技能に青色を付ける

                    // ソート方向を切り替える
                    currentDirection = (currentDirection === 'asc') ? 'desc' : 'asc';
                });
            });
        });

    </script>
@endpush