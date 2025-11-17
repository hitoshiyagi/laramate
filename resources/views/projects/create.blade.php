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

@section('css')
<style>
    /*
     * 1. 全体設定
     */
    body {
        /* OS標準のモダンなフォント設定 */
        font-family: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
    }

    /*
     * 2. カードのモダン化
     */
    .card {
        /* 控えめでモダンなシャドウを追加し、浮遊感を出す */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
        border: none;
        /* デフォルトの境界線を削除 */
        border-radius: 0.75rem;
        /* 角を丸くする */
        margin-bottom: 24px;
        /* マージンを少し増やす */
    }

    .card-header {
        /* ヘッダーの背景を白にし、下線で区切る（AdminLTEのデフォルトを尊重しつつ） */
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        /* フォントを少し太く */
        font-size: 1.15rem;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .form-control,
    .form-select {
        /* 入力フィールドの角を丸くし、フォーカス時のスタイルを改善 */
        border-radius: 0.5rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /*
     * 3. プレビューテーブルのモダン化
     */
    #result-table {
        background-color: #ffffff;
        /* 背景色を白に */
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 0;
        /* 親要素のパディングを削除 */
        overflow: hidden;
        /* 角丸を適用するために必要 */
    }

    #result-table table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    #result-table th,
    #result-table td {
        border: none;
        /* 罫線を削除 */
        border-bottom: 1px solid #e9ecef;
        /* 下線のみ残す */
        padding: 12px 16px;
        /* パディングを増やす */
        vertical-align: middle;
        font-size: 0.95rem;
    }

    #result-table th {
        background-color: #f8f9fa;
        /* ヘッダーの背景を明るいグレーに */
        font-weight: 600;
        color: #495057;
    }

    #result-table tr:last-child td {
        border-bottom: none;
        /* 最後の行の下線を削除 */
    }

    /*
     * 4. ステップカードのデザイン
     */
    .step-card {
        background-color: #f8f9fa;
        /* 明るい背景 */
        border: 1px solid #e3e8ec;
        border-radius: 0.5rem;
        padding: 16px;
        margin-bottom: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .step-card:hover {
        /* ホバーでわずかに持ち上がり、モダンなインタラクションを追加 */
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
    }

    /*
     * 5. コードブロックのモダン化
     */
    .code-block {
        /* モダンなダークテーマ */
        background-color: #2c3e50;
        /* 少し明るいダークブルー */
        color: #ecf0f1;
        /* 明るいテキスト */
        padding: 15px;
        border-radius: 0.5rem;
        font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
        /* モダンなフォント */
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-all;
        margin-top: 10px;
        font-size: 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .code-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 8px;
    }

    .copy-btn {
        /* より目立つが控えめなボタンデザイン */
        background-color: #3498db;
        /* プライマリーブルー */
        border: none;
        color: #fff;
        padding: 6px 12px;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .copy-btn:hover {
        background-color: #2980b9;
        /* ホバー時の色 */
    }

    /* コピー完了時のデザイン */
    .copy-btn.copied {
        background-color: #2ecc71 !important;
        /* 成功の緑 */
    }

    /*
     * 6. ユーティリティとボタン
     */
    #register-elements,
    #clear-elements {
        margin-right: 15px;
        /* ボタン間隔を調整 */
    }

    .btn-primary,
    .btn-success {
        /* ボタンに控えめなシャドウと丸みを適用 */
        border-radius: 0.5rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .btn-primary:hover,
    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    /*
     * 7. レスポンシブ対応の改善
     */
    @media (max-width: 768px) {

        .card {
            border-radius: 0.5rem;
            margin-bottom: 16px;
        }

        #result-table th,
        #result-table td {
            padding: 10px 12px;
            font-size: 0.85rem;
        }

        /* フォームラベルの配置調整 */
        #element-card .col-md-4,
        #project-card .col-md-4 {
            text-align: left !important;
            /* モバイルでは左揃えに */
            margin-bottom: 4px;
        }

        /* ボタンの配置 */
        #element-card .col-md-6.offset-md-4,
        #project-card .col-md-6.offset-md-4 {
            text-align: center;
            margin-left: 0;
            width: 100%;
        }

        /* 登録・クリアボタンの調整 */
        #generation-result .mt-3.col-md-6.offset-md-4 {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: 0;
            width: 100%;
        }

        #register-elements,
        #clear-elements {
            width: 100%;
            margin-right: 0;
            margin-bottom: 8px;
        }

        .step-card {
            padding: 12px;
        }
    }
</style>
@stop

@section('js')
<script src="{{ asset('js/element.js') }}"></script>
@stop