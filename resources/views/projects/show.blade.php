@extends('adminlte::page')

@section('title', 'プロジェクト詳細')

@section('content_header')
<h1>{{ $project->name }} の詳細</h1>
@stop

@section('content')
<div class="container">

    {{-- プロジェクト情報 --}}
    <div class="mb-4 p-3 border rounded shadow-sm bg-light">
        <h3 class="mb-2">{{ $project->name }}</h3>
        <p class="mb-0">
            リポジトリ:
            @if($project->repo_name)
            <a href="{{ $project->repo_name }}" target="_blank">{{ $project->repo_name }}</a>
            @else
            未設定
            @endif
        </p>
        <p class="mb-0 text-muted">
            作成日: {{ $project->created_at->format('Y/m/d') }}
        </p>
    </div>

    {{-- 要素群一覧 --}}
    <h4 class="mb-3">要素一覧</h4>

    @if($project->elements->isEmpty())
    <p>まだ要素が登録されていません。</p>
    <a href="{{ route('elements.create', $project->id) }}" class="btn btn-primary">＋ 要素を追加</a>
    @else
    <div class="row">
        @foreach($project->elements as $element)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 hover-scale">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold text-primary">{{ $element->keyword }}</h5>
                    <p class="mb-1"><strong>環境:</strong> {{ $element->env }}</p>
                    <p class="mb-1"><strong>Laravelバージョン:</strong> {{ $element->laravel_version }}</p>
                    <p class="mb-1"><strong>テーブル名:</strong> {{ $element->table_name }}</p>
                    <p class="mb-1"><strong>モデル名:</strong> {{ $element->model_name }}</p>
                    <p class="mb-1"><strong>コントローラ名:</strong> {{ $element->controller_name }}</p>
                    <p class="mb-1"><strong>DB名:</strong> {{ $element->db_name }}</p>
                    <p class="mb-1"><strong>リポジトリ名:</strong> {{ $element->repo_name }}</p>

                    {{-- 将来的に編集ボタンを追加可能 --}}
                    <div class="mt-auto">
                        <a href="#" class="btn btn-outline-secondary w-100 disabled">編集（準備中）</a>
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