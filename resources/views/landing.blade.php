@extends('layouts.app')

@section('title', 'Laravelポートフォリオを作りながら学べる！')

@section('content')
<div class="container my-5 landing-container">

    {{-- ヒーローセクション --}}
    <header class="text-center py-5 mb-5 hero-section">
        <h1 class="display-4 fw-bold mb-3">
            Laravel 初心者でも、ポートフォリオを作りながら学べる！
        </h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width: 700px;">
            MVC、CRUD、命名規則...もう迷わない。この学習アプリで、実践的なWebアプリ開発スキルを身につけよう。
        </p>
        {{-- CTA ボタン --}}
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill shadow-lg cta-button">
            今すぐ登録して始める <i class="fas fa-angle-right ms-2"></i>
        </a>
    </header>

    <hr class="my-5">

    {{-- ユーザーの悩み --}}
    <section class="mb-5 py-3">
        {{-- 絵文字を削除 --}}
        <h2 class="text-center mb-5 section-title">初学者がぶつかる壁...</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm problem-card">
                    <div class="card-body text-center">
                        <i class="fas fa-question-circle fa-3x text-info mb-3"></i>
                        <h3 class="card-title h5 fw-bold">MVCの構造が複雑で分からない</h3>
                        <p class="card-text text-secondary">「Controller」「Model」「View」の役割分担が理解できず、コードがごちゃごちゃになってしまう。</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm problem-card">
                    <div class="card-body text-center">
                        <i class="fas fa-cubes fa-3x text-info mb-3"></i>
                        <h3 class="card-title h5 fw-bold">CRUDの作成手順や命名規則で迷う</h3>
                        <p class="card-text text-secondary">データベース設計や要素名（テーブル名、モデル名）をどうすればいいか分からず、手が止まる。</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm problem-card">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs fa-3x text-info mb-3"></i>
                        <h3 class="card-title h5 fw-bold">学習用プロジェクトの環境構築が難しい</h3>
                        <p class="card-text text-secondary">教材の通りにやってもエラーが出て、本題に入る前に挫折しそうになる。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="my-5">

    {{-- 機能紹介セクション (悩みの解決策) --}}
    <section class="mb-5 py-3">
        {{-- 絵文字を削除 --}}
        <h2 class="text-center mb-5 section-title">このアプリがあなたの悩みを解決します</h2>
        <div class="row g-5">
            {{-- 機能1: 要素名自動生成 --}}
            <div class="col-md-12">
                <div class="card p-4 feature-card shadow-lg">
                    <div class="card-body">
                        <i class="fas fa-tags fa-2x text-primary float-end"></i>
                        <h3 class="card-title fw-bold text-primary mb-3">迷わない！要素名の自動生成</h3>
                        <p class="card-text lead">作りたいアプリの「テーマ」を入力するだけで、テーブル名、モデル名、コントローラ名**をLaravelの命名規則に沿って自動生成。すぐに開発に取りかかれます。</p>
                        <div class="bg-light p-3 mt-3 border-start border-primary border-5 code-example">
                            <p class="text-muted small mb-1">例: テーマ「ブログ記事」</p>
                            <code class="d-block">
                                Model: <span class="text-primary fw-bold">Post</span>, Controller: <span class="text-primary fw-bold">PostController</span>, テーブル: <span class="text-primary fw-bold">posts</span>
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 機能2: ステップ形式の開発ガイド --}}
            <div class="col-md-12">
                <div class="card p-4 feature-card shadow-lg">
                    <div class="card-body">
                        <i class="fas fa-road fa-2x text-primary float-end"></i>
                        <h3 class="card-title fw-bold text-primary mb-3">手順を確認・コピー！ステップ形式ガイド</h3>
                        <p class="card-text lead">「プロジェクト作成」「モデル作成」「ルート定義」といった開発手順を、ステップ形式で提示。コマンドはワンクリックでコピーでき、すぐに実行可能です。</p>
                        <div class="steps-container mt-4" id="stepsContainer">
                            {{-- JSでステップカードが挿入される --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 機能3: CRUD作成手順の提示 --}}
            <div class="col-md-12">
                <div class="card p-4 feature-card shadow-lg">
                    <div class="card-body">
                        <i class="fas fa-wrench fa-2x text-primary float-end"></i>
                        <h3 class="card-title fw-bold text-primary mb-3">CRUDの「流れ」を体感</h3>
                        <p class="card-text lead">実際にWebアプリでよく使われるCRUD (Create, Read, Update, Delete)の操作を、コードと実行結果で確認。アプリ開発の全体像を掴めます。</p>
                        <div class="bg-light p-3 mt-3 border-start border-success border-5 code-example">
                            <p class="text-muted small mb-1">体感できる流れ:</p>
                            <code class="d-block">
                                Request <i class="fas fa-angle-right mx-1"></i> Route <i class="fas fa-angle-right mx-1"></i>
                                <span class="text-success fw-bold">Controller</span> <i class="fas fa-angle-right mx-1"></i>
                                <span class="text-success fw-bold">Model</span> <i class="fas fa-angle-right mx-1"></i> Database
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="my-5">

    {{-- 学習メリットセクション --}}
    <section class="mb-5 py-3">
        {{-- 絵文字を削除 --}}
        <h2 class="text-center mb-5 section-title">このアプリで得られる成果</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm benefit-card">
                    <div class="card-body d-flex align-items-start">
                        <i class="fas fa-check-circle fa-2x text-success me-3 mt-1"></i>
                        <p class="card-text fw-medium m-0">MVCの構造を体で覚えられる**ので、どんなアプリにも応用可能に。</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm benefit-card">
                    <div class="card-body d-flex align-items-start">
                        <i class="fas fa-lightbulb fa-2x text-warning me-3 mt-1"></i>
                        <p class="card-text fw-medium m-0">「自分にも作れた！」という成功体験を積み、学習へのモチベーションが向上。</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm benefit-card">
                    <div class="card-body d-flex align-items-start">
                        <i class="fas fa-briefcase fa-2x text-info me-3 mt-1"></i>
                        <p class="card-text fw-medium m-0">作成したサンプルの構成は、そのままポートフォリオとして提出可能。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="my-5">

    {{-- CTA 再提示 --}}
    <section class="text-center p-5 rounded-3 cta-bottom">
        <h2 class="section-title text-white mb-4">さあ、Laravel学習を始めよう！</h2>
        <p class="lead text-white-50 mb-5">独学で迷う時間を、**実践的な開発の時間**に変えましょう。</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill shadow-lg cta-button">
            さぁ、今すぐ無料登録！ <i class="fas fa-angle-right ms-2"></i>
        </a>
    </section>

</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
{{-- Font Awesome 6のCDNに変更。こちらの方がWebフォントの読み込みが安定します。 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
{{-- @vite('resources/css/app.css') は Tailwind CSS を想定しますが、今回はBootstrapを使うためコメントアウトします --}}

<style>
    /* カスタムスタイル */
    body {
        font-family: 'Roboto', 'Noto Sans JP', sans-serif;
        background-color: #f8f9fa;
        /* Bootstrapに合わせて調整 */
    }

    .landing-container {
        padding: 0 15px;
        /* Bootstrapのcontainerに合わせる */
    }

    /* ヒーローセクション */
    .hero-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
        /* 薄い青のグラデーション */
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    }

    .hero-section h1 {
        color: #1a73e8;
    }

    /* CTA ボタンを緑に変更 */
    .cta-button {
        background-color: #2ECC71 !important;
        /* 明るい緑 */
        border-color: #2ECC71 !important;
        padding: 1rem 3rem;
        font-size: 1.25rem;
    }

    .cta-button:hover {
        background-color: #27AE60 !important;
        /* 濃い緑 */
        border-color: #27AE60 !important;
    }

    /* セクションタイトル */
    .section-title {
        color: #1a73e8;
        font-weight: 700;
        font-size: 2rem;
    }

    /* カード共通 */
    .card {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    /* 機能紹介カード */
    .feature-card {
        background-color: #ffffff;
        border-left: 8px solid #1a73e8;
        border-radius: 12px;
    }

    /* ステップカード (JSで生成される要素) */
    .step-card {
        background-color: #f1f6ff;
        /* 薄い青の背景 */
        border: 1px solid #d0e0ff;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .step-card h5 {
        color: #1a73e8;
        font-weight: bold;
    }

    .step-card pre {
        background-color: #e3f2fd;
        /* さらに薄い青 */
        padding: 0.8rem;
        border-radius: 6px;
        overflow-x: auto;
        font-size: 0.9rem;
        color: #2c3e50;
        margin-top: 5px;
    }

    .copy-btn {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 6px;
        background-color: #3498db;
        color: #fff;
        cursor: pointer;
        font-size: 0.85rem;
        transition: background 0.3s ease;
        float: right;
        margin-bottom: 5px;
    }

    .copy-btn:hover {
        background-color: #2980b9;
    }

    /* CTA 再提示セクション */
    .cta-bottom {
        background-color: #1a73e8;
        box-shadow: 0 10px 30px rgba(26, 115, 232, 0.5);
    }

    /* フッターのCTAボタンを緑に変更 */
    .cta-button-secondary {
        background-color: #2ECC71 !important;
        /* 明るい緑 */
        border-color: #2ECC71 !important;
        color: #fff !important;
        /* 背景が青なのでテキストを白に */
        font-weight: bold;
    }

    .cta-button-secondary:hover {
        background-color: #27AE60 !important;
        /* 濃い緑 */
        border-color: #27AE60 !important;
    }

    /* Font Awesomeアイコンの色の調整 */
    .problem-card .fa-3x {
        color: #1a73e8;
    }

    .benefit-card .fa-check-circle {
        color: #28a745;
    }

    /* Font Awesome 6ではfa-lightbulb-o は fas fa-lightbulb に統一されています */
    .benefit-card .fa-lightbulb {
        color: #ffc107;
    }

    .benefit-card .fa-briefcase {
        color: #17a2b8;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ====================================
    // デモ用ステップ表示処理
    // ====================================
    const stepsContainer = document.getElementById('stepsContainer');

    // デモ用ステップデータ
    const demoSteps = [{
            title: '① プロジェクト作成 (環境構築)',
            description: '環境構築は一度だけ。',
            command: 'composer create-project laravel/laravel myapp'
        },
        {
            title: '② モデル/マイグレーション生成',
            description: '作りたい要素に対応するファイルを作成します。',
            command: 'php artisan make:model Task -m'
        },
        {
            title: '③ コントローラ生成 (CRUD指定)',
            description: '必要なCRUDメソッドが揃ったコントローラを作成します。',
            command: 'php artisan make:controller TaskController --resource'
        },
    ];

    function displaySteps(steps) {
        stepsContainer.innerHTML = '';
        steps.forEach(step => {
            const div = document.createElement('div');
            div.classList.add('step-card');
            div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">${step.title}</h5>
            </div>
            <p class="text-muted small">${step.description}</p>
            ${step.command ? `<div class="clearfix"><button class="copy-btn">コピー</button></div><pre>${step.command}</pre>` : ''}
        `;
            stepsContainer.appendChild(div);
        });

        // コピー機能のイベントリスナー
        stepsContainer.querySelectorAll('.copy-btn').forEach(btn => {
            // clipboard.writeText()がiframe環境で動作しない場合に備えて、
            // document.execCommand('copy')をフォールバックとして使用します。

            btn.addEventListener('click', (e) => {
                const codeElement = btn.parentElement.nextElementSibling;
                const code = codeElement.innerText;

                // テキストエリアを作成してコピー
                const tempTextArea = document.createElement('textarea');
                tempTextArea.value = code;
                document.body.appendChild(tempTextArea);
                tempTextArea.select();

                let success = false;
                try {
                    // execCommand('copy')は非推奨ですが、iframe環境ではnavigator.clipboardの許可がない場合があるため使用
                    success = document.execCommand('copy');
                } catch (err) {
                    console.error('Copy failed:', err);
                }

                document.body.removeChild(tempTextArea);

                if (success) {
                    btn.innerText = 'コピーしました';
                    setTimeout(() => btn.innerText = 'コピー', 1500);
                } else {
                    // フォールバックが失敗した場合のメッセージ
                    btn.innerText = 'コピー失敗';
                    setTimeout(() => btn.innerText = 'コピー', 1500);
                }
            });
        });
    }

    // ページロード時にステップを表示
    document.addEventListener('DOMContentLoaded', () => {
        displaySteps(demoSteps);
    });
</script>
@endsection