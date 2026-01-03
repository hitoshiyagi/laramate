@extends('adminlte::page')

@section('title', '子要素追加')

@section('content_header')
<h1>子要素追加</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ asset('build/assets/app-96e1218c.css') }}">
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card mt-4">
                <div class="card-header bg-primary text-white">子要素追加</div>
                <div class="card-body">

                    <form id="add-element-form" action="{{ route('elements.store_additional') }}" method="POST">
                        @csrf

                        {{-- プロジェクトID --}}
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        {{-- キーワード（子要素名） --}}
                        <div class="row mb-3">
                            <label for="keyword" class="col-md-4 col-form-label text-md-end">
                                キーワード（子要素名）
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="keyword" id="keyword"
                                    class="form-control"
                                    placeholder="例：member" required>
                                <small class="text-muted">半角英数字のみ。同一プロジェクト内で重複不可。</small>
                                @error('keyword')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- 環境 --}}
                        <div class="row mb-3">
                            <label for="env" class="col-md-4 col-form-label text-md-end">環境</label>
                            <div class="col-md-6">
                                <select name="env" id="env" class="form-control" required>
                                    <option value="">選択してください</option>
                                    <option value="xampp">XAMPP</option>
                                    <option value="mamp">MAMP</option>
                                </select>
                            </div>
                        </div>

                        {{-- Laravelバージョン --}}
                        <div class="row mb-3">
                            <label for="laravel_version" class="col-md-4 col-form-label text-md-end">
                                Laravelバージョン
                            </label>
                            <div class="col-md-6">
                                <select name="laravel_version" id="laravel_version" class="form-control" required>
                                    <option value="">選択してください</option>
                                    <option value="10.*">Laravel 10</option>
                                    <option value="11.*">Laravel 11</option>
                                    <option value="12.*">Laravel 12</option>
                                </select>
                            </div>
                        </div>

                        {{-- テーブル名 --}}
                        <div class="row mb-3">
                            <label for="table_name" class="col-md-4 col-form-label text-md-end">テーブル名</label>
                            <div class="col-md-6">
                                <input type="text" name="table_name" id="table_name" class="form-control"
                                    placeholder="例: user_profiles" required>
                                <small class="text-muted">スネークケースの複数形で入力してください</small>
                                @error('table_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- モデル名 --}}
                        <div class="row mb-3">
                            <label for="model_name" class="col-md-4 col-form-label text-md-end">モデル名</label>
                            <div class="col-md-6">
                                <input type="text" name="model_name" id="model_name" class="form-control"
                                    placeholder="例: UserProfile" required>
                                @error('model_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- コントローラ名 --}}
                        <div class="row mb-3">
                            <label for="controller_name" class="col-md-4 col-form-label text-md-end">
                                コントローラ名
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="controller_name" id="controller_name" class="form-control"
                                    placeholder="例: UserProfileController" required>
                                @error('controller_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ボタン --}}
                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success">追加登録</button>
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-secondary ms-2">
                                    キャンセル
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-4 text-muted">
                                <p>※入力後「追加登録」を押すと、新しい子要素がプロジェクトに追加されます。</p>
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
<script src="{{ asset('js/create_additional.js') }}"></script>
<script src="{{ asset('js/element.js') }}"></script>
<script src="{{ asset('js/project.js') }}"></script>
@stop