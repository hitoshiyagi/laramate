@extends('adminlte::page')

@section('title', '子要素編集')

@section('content_header')
<h1>子要素編集: {{ $element->keyword }}</h1>
@stop

@section('content')
<div class="container">

    <form id="edit-element-form" action="{{ route('elements.update', $element->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- keyword --}}
        <div class="mb-3">
            <label for="keyword" class="form-label">キーワード（子要素名）</label>
            <input type="text" name="keyword" id="keyword" class="form-control"
                value="{{ old('keyword', $element->keyword) }}" required>
            <small class="text-muted">同一プロジェクト内で重複不可</small>
            @error('keyword')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- env (readonly) --}}
        <div class="mb-3">
            <label for="env" class="form-label">環境</label>
            <input type="text" name="env" id="env" class="form-control"
                value="{{ $element->env }}" readonly>
        </div>

        {{-- laravel_version (readonly) --}}
        <div class="mb-3">
            <label for="laravel_version" class="form-label">Laravelバージョン</label>
            <input type="text" name="laravel_version" id="laravel_version" class="form-control"
                value="{{ $element->laravel_version }}" readonly>
        </div>

        {{-- table_name --}}
        <div class="mb-3">
            <label for="table_name" class="form-label">テーブル名</label>
            <input type="text" name="table_name" id="table_name" class="form-control"
                value="{{ old('table_name', $element->table_name) }}" required>
            <small class="text-muted">スネークケースの複数形で入力してください（例: user_profiles）</small>
            @error('table_name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- model_name --}}
        <div class="mb-3">
            <label for="model_name" class="form-label">モデル名</label>
            <input type="text" name="model_name" id="model_name" class="form-control"
                value="{{ old('model_name', $element->model_name) }}" required>
            <small class="text-muted">パスカルケースで入力してください（例: UserProfile）</small>
            @error('model_name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- controller_name --}}
        <div class="mb-3">
            <label for="controller_name" class="form-label">コントローラ名</label>
            <input type="text" name="controller_name" id="controller_name" class="form-control"
                value="{{ old('controller_name', $element->controller_name) }}" required>
            <small class="text-muted">パスカルケース＋Controllerで入力してください（例: UserProfileController）</small>
            @error('controller_name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- submit --}}
        <button type="submit" class="btn btn-primary">更新する</button>
        <a href="{{ route('projects.show', $element->project_id) }}" class="btn btn-secondary ms-2">キャンセル</a>

    </form>
</div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // keyword 半角英数字制限
        const keywordInput = document.getElementById('keyword');
        keywordInput.addEventListener('input', () => {
            keywordInput.value = keywordInput.value.replace(/[^a-zA-Z0-9]/g, '');
        });

        // 他の input も必要なら同様にバリデーションを追加可能
    });
</script>
@stop