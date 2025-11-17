@extends('adminlte::page')

@section('title', '子要素追加')

@section('content_header')
<h1>子要素追加</h1>
@stop

@section('content')
<div class="container">

    <form id="add-element-form" action="{{ route('elements.store_additional') }}" method="POST">
        @csrf

        {{-- プロジェクトID --}}
        <input type="hidden" name="project_id" value="{{ $project->id }}">

        {{-- キーワード（子要素名） --}}
        <div class="mb-3">
            <label for="keyword" class="form-label">キーワード（子要素名）</label>
            <input type="text" name="keyword" id="keyword" class="form-control"
                placeholder="例：member" required>
            <small class="text-muted">半角英数字のみ。同一プロジェクト内で重複不可。</small>
            @error('keyword')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- 環境 --}}
        <div class="mb-3">
            <label for="env" class="form-label">環境</label>
            <select name="env" id="env" class="form-control" required>
                <option value="">選択してください</option>
                <option value="xampp">XAMPP</option>
                <option value="mamp">MAMP</option>
            </select>
        </div>

        {{-- Laravelバージョン --}}
        <div class="mb-3">
            <label for="laravel_version" class="form-label">Laravelバージョン</label>
            <select name="laravel_version" id="laravel_version" class="form-control" required>
                <option value="">選択してください</option>
                <option value="10.*">Laravel 10</option>
                <option value="11.*">Laravel 11</option>
                <option value="12.*">Laravel 12</option>
            </select>
        </div>

        {{-- テーブル名 --}}
        <div class="mb-3">
            <label for="table_name" class="form-label">テーブル名</label>
            <input type="text" name="table_name" id="table_name" class="form-control"
                placeholder="例: user_profiles" required>
            <small class="text-muted">スネークケースの複数形で入力してください</small>
            @error('table_name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- モデル名 --}}
        <div class="mb-3">
            <label for="model_name" class="form-label">モデル名</label>
            <input type="text" name="model_name" id="model_name" class="form-control"
                placeholder="例: UserProfile" required>
            @error('model_name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- コントローラ名 --}}
        <div class="mb-3">
            <label for="controller_name" class="form-label">コントローラ名</label>
            <input type="text" name="controller_name" id="controller_name" class="form-control"
                placeholder="例: UserProfileController" required>
            @error('controller_name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- ボタン --}}
        <button type="submit" class="btn btn-success">追加登録</button>
        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-secondary ms-2">キャンセル</a>

        <div class="mt-3 text-muted">
            <p>※入力後「追加登録」を押すと、新しい子要素がプロジェクトに追加されます。</p>
        </div>
    </form>
</div>
@stop

@section('js')
<script src="{{ asset('js/create_additional.js') }}"></script>
@stop