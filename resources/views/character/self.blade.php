@extends('layout')

@section('content')
<h1>友達のキャラクター</h1>

@include('components.character-registration', ['type' => 'friend'])

<hr>

<!-- 表示切替ボタン -->
<button id="toggle-basic-button" class="toggle-button">基本情報</button>
<button id="toggle-abilities-button" class="toggle-button">能力値</button>
<button id="toggle-skills-button" class="toggle-button">技能</button>

@if($characters->isEmpty())
    <p>まだ友達のキャラクターが登録されていません。</p>
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
                    <td class="toggle-basic">{{ $character->occupation ?? '不明' }}</td>
                    <td class="toggle-basic">{{ $character->age ?? '不明' }}</td>
                    <td class="toggle-basic">{{ $character->sex ?? '不明' }}</td>
                    <td class="toggle-basic">{{ $character->birthplace ?? '不明' }}</td>
                    <td class="toggle-basic">{{ $character->degree ?? '不明' }}</td>
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
                                <div class="skill-item" data-name="{{ $skill['name'] }}" data-value="{{ $skill['value'] }}">
                                    {{ $skill['name'] }}: {{ $skill['value'] }}
                                </div>
                            @endforeach
                        @else
                            <div class="skill-item">技能はありません。</div>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('friend_characters.edit', $character->id) }}" class="btn btn-primary">編集</a>

                        <form action="{{ route('friend_characters.destroy', $character->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">削除</button>
                        </form>

                        <form action="{{ route('friend_characters.copy', $character->id) }}" method="POST"
                            style="display:inline;">
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
        let currentSkillDirection = 'asc';

        function sortSkills(skillName) {
            const table = document.getElementById("characterTable");
            const rows = Array.from(table.querySelectorAll('tbody > tr'));
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

                return (currentSkillDirection === 'asc') ? aValue - bValue : bValue - aValue;
            });

            rows.forEach(row => table.querySelector('tbody').appendChild(row));
            currentSkillDirection = (currentSkillDirection === 'asc') ? 'desc' : 'asc';
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-abilities').forEach(el => el.style.display = 'table-cell');
            document.querySelectorAll('.toggle-basic').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.toggle-skills').forEach(el => el.style.display = 'none');
        });

        document.getElementById('toggle-basic-button').addEventListener('click', function () {
            document.querySelectorAll('.toggle-basic').forEach(el => {
                el.style.display = el.style.display === 'none' ? 'table-cell' : 'none';
            });
            document.querySelectorAll('.toggle-abilities').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.toggle-skills').forEach(el => el.style.display = 'none');
        });

        document.getElementById('toggle-abilities-button').addEventListener('click', function () {
            document.querySelectorAll('.toggle-abilities').forEach(el => {
                el.style.display = el.style.display === 'none' ? 'table-cell' : 'none';
            });
            document.querySelectorAll('.toggle-basic').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.toggle-skills').forEach(el => el.style.display = 'none');
        });

        document.getElementById('toggle-skills-button').addEventListener('click', function () {
            document.querySelectorAll('.toggle-skills').forEach(el => {
                el.style.display = el.style.display === 'none' ? 'table-cell' : 'none';
            });
            document.querySelectorAll('.toggle-basic').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.toggle-abilities').forEach(el => el.style.display = 'none');
        });
    </script>
@endpush