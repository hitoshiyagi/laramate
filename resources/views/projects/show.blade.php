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
                        <h5 class="fw-bold text-primary me-3">{{$element->keyword }}</h5>
                        <div class="d-flex flex-shrink-0">
                            @if($element->project)
                            <a href="{{ route('elements.edit', $element->id) }}"
                                class="btn border-0 bg-transparent text-secondary mr-3"
                                title="子要素を編集"
                                style="font-size: 1.6rem;">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            @endif
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
                </div>