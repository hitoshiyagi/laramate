@extends('adminlte::page')

@section('title', '要素追加')

@section('content_header')
<h1>要素を追加</h1>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row justify-content-center">
    <div class="col-md-10">

        {{-- 要素名生成カード --}}
        <div class="card mt-4" id="element-card">
            <div class="card-header">要素名生成</div>
            <div class="card-body">
                <form id="element-form">
                    <input type="hidden" id="project-id" value="{{ $project->id }}">

                    {{-- プロジェクト情報 --}}
                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                        <div class="col-md-6">
                            <input type="text" id="element-project-name" class="form-control" value="{{ $project->name }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">リポジトリ名</label>
                        <div class="col-md-6">
                            <input type="text" id="element-project-repo" class="form-control" value="{{ $project->repo }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">データベース名</label>
                        <div class="col-md-6">
                            <input type="text" id="element-project-db" class="form-control" value="{{ $project->database_name }}" readonly>
                        </div>
                    </div>

                    {{-- キーワード --}}
                    <div class="row mb-3">
                        <label for="keyword" class="col-md-4 col-form-label text-md-end">要素名キーワード</label>
                        <div class="col-md-6">
                            <input type="text" name="keyword" id="keyword" class="form-control" placeholder="例：member" pattern="[a-zA-Z0-9]*" inputmode="latin" autocomplete="off" required>
                            <small class="form-text text-muted">半角英数字のみで入力してください</small>
                        </div>
                    </div>

                    {{-- 開発環境 --}}
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

                    {{-- Laravelバージョン --}}
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

                    {{-- ボタン --}}
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
        </div>

        {{-- 手順／ヒント表示 --}}
        <div id="generation-steps" class="mt-4" style="display:none;"></div>

    </div>
</div>
@stop

@section('css')
<style>
    .step-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
    }

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
</style>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const keywordInput = document.getElementById("keyword");
        const envSelect = document.getElementById("env-select");
        const laravelVersionSelect = document.getElementById("laravel-version");
        const elementProjectName = document.getElementById("element-project-name");
        const elementProjectRepo = document.getElementById("element-project-repo");
        const elementProjectDb = document.getElementById("element-project-db");

        const previewBtn = document.getElementById("preview-elements");
        const registerBtn = document.getElementById("register-elements");
        const clearBtn = document.getElementById("clear-elements");

        const resultTable = document.getElementById("result-table");
        const generationResult = document.getElementById("generation-result");
        const stepsContainer = document.getElementById("generation-steps");
        const messageDiv = document.getElementById("generation-message");

        let Table, Model, Controller, DB;

        function pluralize(word) {
            word = word.toLowerCase();
            if (word.endsWith("y")) return word.slice(0, -1) + "ies";
            if (["s", "x", "z"].includes(word.slice(-1)) || word.endsWith("ch") || word.endsWith("sh"))
                return word + "es";
            return word + "s";
        }

        previewBtn?.addEventListener("click", () => {
            const keyword = keywordInput.value.trim();
            const env = envSelect.value;
            const laravelVersion = laravelVersionSelect.value;
            const projectName = elementProjectName.value.trim();
            const dbName = elementProjectDb.value.trim();
            if (!keyword || !env || !laravelVersion)
                return alert("キーワード・環境・Laravelバージョンを選択してください");

            Table = pluralize(keyword);
            Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
            Controller = Model + "Controller";
            DB = dbName;

            resultTable.innerHTML = `
            <table class="table table-bordered table-striped mt-3">
                <thead><tr><th>項目</th><th>生成結果</th></tr></thead>
                <tbody>
                    <tr><td>プロジェクト名</td><td>${projectName}</td></tr>
                    <tr><td>GitHubリポジトリ名</td><td>${elementProjectRepo.value}</td></tr>
                    <tr><td>データベース名</td><td>${DB}</td></tr>
                    <tr><td>モデル名</td><td>${Model}</td></tr>
                    <tr><td>テーブル名</td><td>${Table}</td></tr>
                    <tr><td>コントローラ名</td><td>${Controller}</td></tr>
                    <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
                </tbody>
            </table>
        `;
            generationResult.style.display = "block";
            resultTable.scrollIntoView({
                behavior: "smooth"
            });
        });

        registerBtn?.addEventListener("click", async () => {
            const projectName = elementProjectName.value.trim();
            const keyword = keywordInput.value.trim();
            const env = envSelect.value;
            const laravelVersion = laravelVersionSelect.value;
            if (!projectName || !keyword || !env || !laravelVersion)
                return alert("すべての項目を入力してください。");

            try {
                const payload = {
                    project_name: projectName,
                    keyword,
                    env,
                    laravel_version: laravelVersion,
                    table_name: Table,
                    model_name: Model,
                    controller_name: Controller,
                    database_name: DB
                };
                const res = await fetch("/elements", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();
                messageDiv.style.display = "block";
                if (data.success) {
                    messageDiv.textContent = "要素を登録しました。";

                    stepsContainer.innerHTML = "";

                    // 新規登録の場合は手順表示
                    if (data.steps && data.steps.length > 0) {
                        data.steps.forEach(step => {
                            const div = document.createElement("div");
                            div.className = "step-card";
                            div.innerHTML = `
                            <h5 class="fw-bold">${step.title}</h5>
                            <p>${step.description}</p>
                            ${step.command ? `<pre class="code-block">${step.command}</pre>` : ''}
                        `;
                            stepsContainer.appendChild(div);
                        });
                        stepsContainer.style.display = "block";
                    }
                    // 追加登録の場合はヒント表示
                    else if (data.hints && data.hints.length > 0) {
                        data.hints.forEach(hint => {
                            const div = document.createElement("div");
                            div.className = "step-card";
                            div.innerHTML = `<p>${hint}</p>`;
                            stepsContainer.appendChild(div);
                        });
                        stepsContainer.style.display = "block";
                    }

                    stepsContainer.scrollIntoView({
                        behavior: "smooth"
                    });
                } else {
                    alert(data.message || "登録に失敗しました。");
                }
            } catch (err) {
                console.error(err);
                alert("通信エラーが発生しました");
            }
        });

        clearBtn?.addEventListener("click", () => {
            keywordInput.value = "";
            envSelect.value = "";
            laravelVersionSelect.value = "";
            resultTable.innerHTML = "";
            generationResult.style.display = "none";
            stepsContainer.style.display = "none";
            messageDiv.style.display = "none";
        });
    });
</script>
@stop