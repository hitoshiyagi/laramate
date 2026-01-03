@extends('adminlte::page')

@section('title', 'プロジェクト詳細')

@section('content_header')
<h1>{{ $project->name }} の詳細</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ asset('build/assets/app-96e1218c.css') }}">
@stop

@section('content')
<div class="container">

    {{-- プロジェクト情報 --}}
    <div class="mb-4 p-3 border rounded shadow-sm bg-light">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h3 class="mb-0">{{ $project->name }}</h3>
        </div>

        <p class="mb-0">
            GitHubリポジトリ: {{ $project->repo ?? '未設定' }}
        </p>

        <p class="mb-0">
            データベース名: {{ $project->database_name ?? '未設定' }}
        </p>

        <div class="d-flex align-items-center justify-content-between">
            <p class="mb-0 text-muted">
                作成日: {{ $project->created_at->format('Y/m/d') }}
            </p>

            <button class="btn delete-project border-0 bg-transparent text-secondary"
                data-id="{{ $project->id }}"
                title="プロジェクトを削除"
                style="font-size: 1.6rem;">
                <i class="fa fa-trash-o"></i>
            </button>
        </div>
    </div>

    {{-- 要素一覧 --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">要素一覧</h4>
        <a href="{{ route('elements.create_additional', $project->id) }}" class="btn btn-primary">
            ＋ 要素を追加
        </a>
    </div>

    @if($project->elements->isEmpty())
    <p>まだ要素が登録されていません。</p>
    @else
    <div class="row">
        @foreach($project->elements as $element)
        <div class="col-md-6 mb-4" id="element-{{ $element->id }}">
            <div class="card shadow-sm h-100 hover-scale">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold text-primary me-3">{{ $element->keyword }}</h5>
                        <div class="d-flex flex-shrink-0">
                            <a href="{{ route('elements.edit', $element->id) }}"
                                class="btn border-0 bg-transparent text-secondary me-3"
                                title="子要素を編集"
                                style="font-size: 1.6rem;">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            <button class="btn delete-element-icon border-0 bg-transparent text-secondary"
                                data-id="{{ $element->id }}"
                                title="子要素を削除"
                                style="font-size: 1.6rem;">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </div>
                    </div>

                    @php
                    $fields = [
                    '環境' => $element->env,
                    'Laravelバージョン' => $element->laravel_version,
                    'テーブル名' => $element->table_name,
                    'モデル名' => $element->model_name,
                    'コントローラ名' => $element->controller_name,
                    '作成日' => $element->created_at->format('Y/m/d'),
                    ];
                    @endphp

                    <table class="table table-bordered table-sm">
                        <tbody>
                            @foreach($fields as $label => $value)
                            <tr>
                                <th class="text-nowrap">{{ $label }}</th>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.col-md-6 -->
        @endforeach
    </div> <!-- /.row -->
    @endif

</div> <!-- /.container -->
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // プロジェクト削除
        document.querySelectorAll('.delete-project').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                if (!id) return;
                if (!confirm('プロジェクトを削除します。よろしいですか？')) return;

                try {
                    const res = await fetch(`/projects/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('プロジェクトを削除しました');
                        location.href = '/projects';
                    } else {
                        alert(data.message || '削除できませんでした');
                    }
                } catch (e) {
                    console.error(e);
                    alert('削除に失敗しました');
                }
            });
        });

        // 子要素削除
        document.querySelectorAll('.delete-element-icon').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                if (!id) return;
                if (!confirm('子要素を削除します。よろしいですか？')) return;

                try {
                    const res = await fetch(`/elements/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        document.getElementById(`element-${id}`)?.remove();
                    } else {
                        alert(data.message || '削除できませんでした');
                    }
                } catch (e) {
                    console.error(e);
                    alert('削除に失敗しました');
                }
            });
        });
    });
</script>
@stop