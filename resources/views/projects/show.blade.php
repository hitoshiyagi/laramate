@extends('adminlte::page')

@section('title', 'プロジェクト詳細')

@section('content_header')
<h1>{{ $project->name }} の詳細</h1>
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

        <div class="d-flex align-items-center justify-content-between">
            <p class="mb-0 text-muted">
                作成日: {{ $project->created_at->format('Y/m/d') }}
            </p>

            <button class="delete-project btn p-1 border-0 bg-transparent text-danger"
                data-id="{{ $project->id }}"
                title="プロジェクトを削除"
                style="font-size: 1rem;">
                🗑️
            </button>
        </div>
    </div>

    {{-- 要素一覧 --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">要素一覧</h4>
        <a href="#" class="btn btn-outline-secondary disabled">＋ 要素を追加（準備中）</a>
    </div>

    @if($project->elements->isEmpty())
    <p>まだ要素が登録されていません。</p>
    @else
    <div class="row">
        @foreach($project->elements as $element)
        <div class="col-md-6 mb-4" id="element-{{ $element->id }}">
            <div class="card shadow-sm h-100 hover-scale">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold text-primary mb-0">{{ $element->keyword }}</h5>
                        <button class="delete-element-icon btn p-1 border-0 bg-transparent text-danger"
                            data-id="{{ $element->id }}"
                            title="子要素を削除"
                            style="font-size: 1.1rem;">
                            🗑️
                        </button>
                    </div>

                    @php
                    $fields = [
                    '環境' => $element->env,
                    'Laravelバージョン' => $element->laravel_version,
                    'データベース名' => $element->db_name,
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

                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@stop

@section('css')
<style>
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-scale:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@stop

@section('js')
<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/project.js') }}"></script>
<script src="{{ asset('js/element.js') }}"></script>
@stop