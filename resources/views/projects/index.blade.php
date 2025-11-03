@extends('adminlte::page')

@section('title', '新規プロジェクト作成')

@section('content_header')
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stop


@section('content')
<div class="container" data-page="welcome">
    <div class="row justify-content-center">
        <div class="col-md-10">

            {{-- ✅ プロジェクト作成カード --}}
            <div class="card mt-4" id="project-card">
                <div class="card-header">プロジェクト作成</div>
                <div class="card-body">
                    <p>プロジェクト名を入力し、新規プロジェクトを作成します。</p>

                    <div class="row mb-3">
                        <label for="name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                        <div class="col-md-6">
                            <input type="text" name="name" id="name" class="form-control" placeholder="例：laramate" required>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-primary" id="create-project-btn">登録する</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ 要素群生成カード（最初は非表示） --}}
            <div class="card mt-4" id="element-card" style="display:none;">
                <div class="card-header">
                    要素群生成
                </div>
                <div class="card-body">
                    <form id="element-form">
                        <div class="row mb-3">
                            <label id="project-name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                            <div class="col-md-6">
                                <input type="text" id="element-project-name" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="keyword" class="col-md-4 col-form-label text-md-end">要素群キーワード</label>
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
                                <button type="submit" class="btn btn-primary" id="generate-elements">生成する</button>
                            </div>
                        </div>
                    </form>

                    {{-- ✅ 生成結果表示エリア --}}
                    <div id="generation-result" class="mt-4" style="display:none;">
                        <h5>生成結果</h5>
                        <pre id="result-output" class="p-3 bg-light border rounded"></pre>

                        <h5 class="mt-3">手順</h5>
                        <ol id="generation-steps" class="ps-4"></ol>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@section('js')
<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/project.js') }}"></script>
<script src="{{ asset('js/element.js') }}"></script>
@stop
@stop