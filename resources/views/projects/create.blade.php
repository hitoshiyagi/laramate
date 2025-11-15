@extends('adminlte::page')

@section('title', '新規プロジェクト作成')

@section('content_header')
<h1>新規プロジェクト作成</h1>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row justify-content-center">
    <div class="col-md-10">

        {{-- プロジェクト作成カード --}}
        <div class="card mt-4" id="project-card">
            <div class="card-header">プロジェクト作成</div>
            <div class="card-body">
                <p>プロジェクト名を入力し、新規プロジェクトを作成します。</p>
                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="project-name" class="form-control"
                            placeholder="例：laramate" required pattern="^[a-zA-Z0-9]+$"
                            title="半角英数字のみ入力可能です">
                        <small class="form-text text-muted">半角英数字のみで入力してください（例：laramate）</small>
                        <div id="project-error" class="text-danger mt-1"></div>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="button" class="btn btn-primary" id="create-project-btn">
                            要素名の生成へ進む
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 要素名生成カード --}}
        <div class="card mt-4" id="element-card" style="display:none;">
            <div class="card-header">要素名生成</div>
            <div class="card-body">
                <form id="element-form">
                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                        <div class="col-md-6">
                            <input type="text" id="element-project-name" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">リポジトリ名</label>
                        <div class="col-md-6">
                            <input type="text" id="element-project-repo" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="keyword" class="col-md-4 col-form-label text-md-end">要素名キーワード</label>
                        <div class="col-md-6">
                            <input type="text" name="keyword" id="keyword" class="form-control"
                                placeholder="例：member" required pattern="^[a-zA-Z0-9]+$"
                                title="半角英数字のみ入力可能です">
                            <small class="form-text text-muted">半角英数字のみで入力してください（例：member）</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="env-select" class="col-md-4 col-form-label text-md-end">開発環境</label>
                        <div class="col-md-6">
                            <select name="env" id="env-select" class="form-control" required>
                                <option value="" disabled selected>選択してください</option>
                                <option value="xampp">XAMPP</option>
                                <option value="mamp">MAMP</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="laravel-version" class="col-md-4 col-form-label text-md-end">Laravelバージョン</label>
                        <div class="col-md-6">
                            <select name="laravel_version" id="laravel-version" class="form-control" required>
                                <option value="">選択してください</option>
                                <option value="10.*">10系</option>
                                <option value="11.*">11系</option>
                                <option value="12.*">12系</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-success" id="preview-elements">要素名を生成する</button>
                        </div>
                    </div>
                </form>

                {{-- プレビュー --}}
                <div id="generation-result" class="mt-4" style="display:none;">
                    <div id="result-table" class="bg-light p-3 rounded"></div>

                    <div class="mt-3 col-md-6 offset-md-4">
                        <button class="btn btn-primary" id="register-elements">登録する</button>
                        <button class="btn btn-secondary" id="clear-elements">クリア</button>
                        <div id="generation-message" class="mt-2 text-success" style="display:none;"></div>
                    </div>

                    {{-- 登録後手順 --}}
                    <div id="generation-steps-area" style="display:none;" class="mt-4">
                        <h5>登録後の手順</h5>
                        <div id="generation-steps" class="d-flex flex-column mb-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    /* 手順カード */
    .step-card {
        margin-bottom: 15px;
    }

    .code-block {
        background: #2d2d2d;
        color: #f8f8f2;
        padding: 15px;
        font-family: "Fira Code", monospace;
        overflow-x: auto;
        border-radius: 0 0 4px 4px;
        margin-bottom: 0 !important;
    }

    .code-header {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        background: #444;
        padding: 4px 8px;
        border-radius: 4px 4px 0 0;
        font-size: 0.85rem;
        color: #fff;
    }

    .code-header .copy-btn {
        background: #555;
        /* 初期ボタン色 */
        color: #fff;
        border: none;
        padding: 3px 6px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: background 0.2s;
    }

    .code-header .copy-btn:hover {
        background: #0d6efd;
</style>
@stop

@section('js')
<script src="{{ asset('js/element.js') }}"></script>
@stop