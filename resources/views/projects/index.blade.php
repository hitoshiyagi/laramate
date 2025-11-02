@extends('adminlte::page')

@section('title', '新規プロジェクト作成')

@section('content_header')
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stop

@section('js')
<script src="{{ asset('js/script.js') }}" defer></script>
@stop

@section('content')
<div class="container" data-page="welcome">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- プロジェクト作成カード --}}
            <div class="card">
                <div class="card-header">プロジェクト作成</div>
                <div class="card-body">
                    <p>プロジェクト名を入力し、新規プロジェクトを作成します。</p>

                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                            <div class="col-md-6">
                                <input type="text" name="name" id="name" class="form-control" placeholder="例：laramate" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">登録する</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- プロジェクトが登録済みなら要素群登録フォームを表示 --}}
            @if(isset($project))
            <div class="card mt-4">
                <div class="card-header">要素群生成（{{ $project->name }}）</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('elements.store', ['project' => $project->id]) }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="keyword" class="col-md-4 col-form-label text-md-end">キーワード</label>
                            <div class="col-md-6">
                                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="例：member" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="env-select" class="col-md-4 col-form-label text-md-end">開発環境</label>
                            <div class="col-md-6">
                                <select name="env" id="env-select" class="form-select" required>
                                    <option value="" disabled selected>選択してください</option>
                                    <option value="xampp">XAMPP</option>
                                    <option value="mamp">MAMP</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="laravel-version" class="col-md-4 col-form-label text-md-end">Laravelバージョン</label>
                            <div class="col-md-6">
                                <select name="laravel_version" id="laravel-version" class="form-select" required>
                                    <option value="">選択してください</option>
                                    <option value="10.*">10系</option>
                                    <option value="11.*">11系</option>
                                    <option value="12.*">12系</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success">生成する</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@stop