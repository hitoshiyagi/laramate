@extends('adminlte::page')

@section('title', '新規プロジェクト作成')

@section('content_header')
<h1>新規プロジェクト作成</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .step-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .step-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    /* タイトル部分 */
    .step-card h5 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    /* --- コピー用ヘッダー --- */
    .code-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 8px;
    }

    /* コピーするボタン */
    .copy-btn {
        padding: 6px 12px;
        background: #1976d2;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s ease, box-shadow 0.2s ease;
    }

    .copy-btn:hover {
        background: #1565c0;
        box-shadow: 0 2px 6px rgba(25, 118, 210, 0.35);
    }

    /* --- コードブロック(pre) --- */
    .code-block {
        background: #f5f5f5;
        padding: 14px 18px;
        border-radius: 8px;
        font-family: "Fira Code", Consolas, monospace;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #333;
        overflow-x: auto;
        white-space: pre;
        border-left: 4px solid #1976d2;
    }

    /* スクロールバー（Chrome用） */
    .code-block::-webkit-scrollbar {
        height: 6px;
    }

    .code-block::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 3px;
    }

    .code-block::-webkit-scrollbar-track {
        background: transparent;
    }
</style>
@vite('resources/css/app.css')
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row justify-content-center">
    <div class="col-md-10">

        {{-- プロジェクト作成カード --}}
        <div class="card mt-4" id="project-card">
            <div class="card-header bg-primary text-white">プロジェクト作成</div>

            <div class="card-body">
                <div id="project-error" class="text-danger mb-2"></div>

                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                    <div class="col-md-6">
                        <input type="text" id="name" class="form-control" pattern="[a-zA-Z0-9]*"
                            inputmode="latin"
                            autocomplete="off"
                            placeholder="例: laramate" placeholder="例：laramate">
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
            <div class="card-header bg-primary">要素名生成</div>
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
                                placeholder="例：member" pattern="[a-zA-Z0-9]*"
                                inputmode="latin"
                                autocomplete="off"
                                placeholder="例: member"
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


@section('js')
<script src="{{ asset('js/element.js') }}"></script>
<script src="{{ asset('js/project.js') }}"></script>
@stop