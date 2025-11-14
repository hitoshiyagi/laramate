@extends('adminlte::page')

@section('title', 'プロジェクト詳細')

@section('content_header')
<h1>{{ $project->name }} の詳細</h1>
@stop

@section('content')
<div class="container">

    {{-- プロジェクト情報 --}}
    <div class="mb-4 p-3 border rounded shadow-sm bg-light">
        <h3 class="mb-2">プロジェクト名: {{ $project->name }}</h3>
        <p class="mb-0">
            GitHubリポジトリ:
            {{ $project->repo ?? '未設定' }}
        </p>
        <p class="mb-0 text-muted">
            作成日: {{ $project->created_at->format('Y/m/d') }}
        </p>
    </div>

    {{-- 要素群一覧 --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">要素一覧</h4>
        <a href="{{ route('elements.create', $project->id) }}" class="btn btn btn-outline-secondary">＋ 要素を追加（準備中）</a>
    </div>
    @if($project->elements->isEmpty())
    <p>まだ要素が登録されていません。</p>
    <a href="{{ route('elements.create', $project->id) }}" class="btn btn btn-outline-secondary">＋ 要素を追加（準備中）</a>
    @else
    <div class="row">
        @foreach($project->elements as $element)
        <div class="col-md-6 mb-4" id="element-{{ $element->id }}">
            <div class="card shadow-sm h-100 hover-scale">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold text-primary">{{ $element->keyword }}</h5>
                    @php
                    $fields = [
                    '環境' => $element->env,
                    'Laravelバージョン' => $element->laravel_version,
                    'テーブル名' => $element->table_name,
                    'モデル名' => $element->model_name,
                    'コントローラ名' => $element->controller_name,
                    'データベース名' => $element->db_name,
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
                    {{-- 将来的に編集ボタンを横並びに --}}
                    <div class="mt-auto d-flex flex-column flex-md-row">
                        <a href="#" class="btn btn-outline-secondary flex-fill mb-2 mb-md-0 mr-md-2 disabled">編集</a>

                        <button class="btn btn-outline-danger flex-fill delete-element"
                            data-id="{{ $element->id }}">
                            削除
                        </button>
                    </div>

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