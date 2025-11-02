@extends('adminlte::page')

@section('title', 'プロジェクト詳細')

@section('content_header')
<h1>{{ $project->name }} の詳細</h1>
@stop

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h3>GitHubリポジトリ</h3>
        </div>
        <div class="card-body">
            <p><strong>リポジトリ名：</strong> {{ $project->repository_name ?? '未登録' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>要素一覧</h3>
        </div>
        <div class="card-body">
            @if ($elements->isEmpty())
            <p>まだ要素が登録されていません。</p>
            <a href="{{ route('elements.create', $project->id) }}" class="btn btn-primary">要素を追加する</a>
            @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>要素名</th>
                        <th>DB名</th>
                        <th>モデル名</th>
                        <th>関連テーブル</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($elements as $element)
                    <tr>
                        <td>{{ $element->id }}</td>
                        <td>{{ $element->name }}</td>
                        <td>{{ $element->db }}</td>
                        <td>{{ $element->model }}</td>
                        <td>{{ $element->table }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('elements.create', $project->id) }}" class="btn btn-primary mt-3">要素を追加する</a>
            @endif
        </div>
    </div>
</div>
@stop