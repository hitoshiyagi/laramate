@extends('adminlte::page')

@section('title', 'プロジェクト一覧')

@section('content_header')
<h1>あなたのプロジェクト一覧</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
@section('content')
<div class="container">

    @if($projects->isEmpty())
    <p>まだプロジェクトが登録されていません。</p>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        ＋ 新しいプロジェクトを作成
    </a>
    @else
    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-4 mb-4" id="project-{{ $project->id }}">
            <div class="card shadow-sm border-0 h-100 hover-scale">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold text-primary mb-0">{{ $project->name }}</h5>
                        <button class=" delete-project btn p-0 border-0 bg-transparent text-secondary"
                            data-id="{{ $project->id }}"
                            title="プロジェクトを削除"
                            style="font-size: 1.4rem;">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </div>
                    <p class="text-muted small mb-3">作成日: {{ $project->created_at->format('Y/m/d') }}</p>
                    <div class="mt-auto">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-primary w-100">
                            要素一覧を見る
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@stop

@section('js')
<script src="{{ asset('js/project.js') }}"></script>
<script src="{{ asset('js/element.js') }}"></script>
@stop