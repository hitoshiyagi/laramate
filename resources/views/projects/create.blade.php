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
                <div id="project-error" class="text-danger mb-2"></div>

                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                    <div class="col-md-6">
                        <input type="text" id="name" class="form-control" placeholder="例：laramate">
                        <small class="form-text text-muted">半角英数字のみで入力してください（例：laramate）</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 offset-md-4">
                        <button type="button" id="create-project-btn" class="btn btn-primary">プロジェクトを作成</button>
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
                        <label class="col-md-4 col-form-label text-md-end">データベース名</label>
                        <div class="col-md-6">
                            <input type="text" id="element-project-db" class="form-control" readonly>
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
            </div>
        </div>

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
@stop

@section('css')
<style>
    /* カード全体のマージン調整 */
    .card {
        margin-bottom: 20px;
    }

    /* プレビューのテーブル */
    #result-table table {
        width: 100%;
        border-collapse: collapse;
    }

    #result-table th,
    #result-table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
    }

    #result-table th {
        background-color: #f8f9fa;
    }

    /* ステップカード */
    .step-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
        position: relative;
    }

    /* コードブロック */
    .code-block {
        background-color: #272822;
        color: #f8f8f2;
        padding: 10px;
        border-radius: 4px;
        font-family: 'Courier New', Courier, monospace;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-all;
        margin-top: 5px;
    }

    /* コードヘッダーとコピーボタン */
    .code-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 5px;
    }

    .copy-btn {
        background-color: #6c757d;
        border: none;
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .copy-btn:hover {
        background-color: #5a6268;
    }

    /* コピー完了時に色を変える */
    .copy-btn.copied {
        background-color: #28a745 !important;
    }

    /* 全体の余白 */
    #generation-result {
        margin-top: 20px;
    }

    /* ボタン間隔 */
    #register-elements,
    #clear-elements {
        margin-right: 10px;
    }

    /* ====== レスポンシブ対応 ====== */
    @media (max-width: 768px) {

        #result-table table,
        #result-table th,
        #result-table td {
            font-size: 0.9rem;
        }

        .step-card {
            padding: 10px;
        }

        .copy-btn {
            padding: 3px 6px;
            font-size: 0.8rem;
        }

        #element-card .row.mb-3,
        #project-card .row.mb-3 {
            flex-direction: column;
        }

        #element-card .col-md-6,
        #project-card .col-md-6 {
            width: 100%;
            margin-top: 5px;
        }

        #element-card .col-md-6.offset-md-4,
        #project-card .col-md-6.offset-md-4 {
            margin-left: 0;
        }
    }
</style>
@stop

@section('js')
<script src="{{ asset('js/element.js') }}"></script>
@stop