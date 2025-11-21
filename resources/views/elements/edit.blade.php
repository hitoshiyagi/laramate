@extends('adminlte::page')

@section('title', '子要素編集')

@section('content_header')
<h1>子要素編集: {{ $element->keyword }}</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@vite('resources/css/app.css')
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card mt-4">
                <div class="card-header bg-primary text-white">子要素編集</div>
                <div class="card-body">

                    <form id="edit-element-form" action="{{ route('elements.update', $element->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- keyword --}}
                        <div class="row mb-3">
                            <label for="keyword" class="col-md-4 col-form-label text-md-end">
                                キーワード（子要素名）
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="keyword" id="keyword" class="form-control"
                                    value="{{ old('keyword', $element->keyword) }}" required>
                                <small class="text-muted">同一プロジェクト内で重複不可</small>
                                @error('keyword')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- env (readonly) --}}
                        <div class="row mb-3">
                            <label for="env" class="col-md-4 col-form-label text-md-end">環境</label>
                            <div class="col-md-6">
                                <input type="text" name="env" id="env" class="form-control"
                                    value="{{ $element->env }}" readonly>
                            </div>
                        </div>

                        {{-- laravel_version (readonly) --}}
                        <div class="row mb-3">
                            <label for="laravel_version" class="col-md-4 col-form-label text-md-end">
                                Laravelバージョン
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="laravel_version" id="laravel_version" class="form-control"
                                    value="{{ $element->laravel_version }}" readonly>
                            </div>
                        </div>

                        {{-- table_name --}}
                        <div class="row mb-3">
                            <label for="table_name" class="col-md-4 col-form-label text-md-end">テーブル名</label>
                            <div class="col-md-6">
                                <input type="text" name="table_name" id="table_name" class="form-control"
                                    value="{{ old('table_name', $element->table_name) }}" required>
                                <small class="text-muted">スネークケースの複数形で入力してください（例: user_profiles）</small>
                                @error('table_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- model_name --}}
                        <div class="row mb-3">
                            <label for="model_name" class="col-md-4 col-form-label text-md-end">モデル名</label>
                            <div class="col-md-6">
                                <input type="text" name="model_name" id="model_name" class="form-control"
                                    value="{{ old('model_name', $element->model_name) }}" required>
                                <small class="text-muted">パスカルケースで入力してください（例: UserProfile）</small>
                                @error('model_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- controller_name --}}
                        <div class="row mb-3">
                            <label for="controller_name" class="col-md-4 col-form-label text-md-end">コントローラ名</label>
                            <div class="col-md-6">
                                <input type="text" name="controller_name" id="controller_name" class="form-control"
                                    value="{{ old('controller_name', $element->controller_name) }}" required>
                                <small class="text-muted">パスカルケース＋Controllerで入力してください（例: UserProfileController）</small>
                                @error('controller_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- submit --}}
                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">更新する</button>
                                <a href="{{ route('projects.show', $element->project_id) }}" class="btn btn-secondary ms-2">キャンセル</a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
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