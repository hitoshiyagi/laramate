@extends('layouts.login')

@section('content')
<div class="container" data-page="welcome">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">キーワード生成</div>

                <div class="card-body">
                    <p>キーワードを入力すると、モデル・テーブル・コントローラ・ビュー・DB名を自動生成し、手順に反映します。</p>
                    <p>手順に沿って進めることで、ミスなくLaravelの新規プロジェクトを構築できます。</p>

                    <!-- キーワード入力 -->
                    <div class="row mb-3">
                        <label for="keyword" class="col-md-4 col-form-label text-md-end">キーワード</label>
                        <div class="col-md-6">
                            <input type="text" id="keyword" class="form-control" placeholder="例：member">
                        </div>
                    </div>

                    <!-- 開発環境選択 -->
                    <div class="row mb-3">
                        <label for="env-select" class="col-md-4 col-form-label text-md-end">開発環境</label>
                        <div class="col-md-6">
                            <select id="env-select" class="form-select">
                                <option value="" disabled selected>選択してください</option>
                                <option value="xampp">XAMPP</option>
                                <option value="mamp">MAMP</option>
                            </select>
                        </div>
                    </div>

                    <!-- Laravelバージョン -->
                    <div class="row mb-3">
                        <label for="laravel-version" class="col-md-4 col-form-label text-md-end">Laravelバージョン</label>
                        <div class="col-md-6">
                            <select id="laravel-version" class="form-select">
                                <option value="">選択してください</option>
                                <option value="10.*">10系</option>
                                <option value="11.*">11系</option>
                                <option value="12.*">12系</option>
                            </select>
                        </div>
                    </div>

                    <!-- ボタン -->
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-primary" onclick="generate()">生成する</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div id="result-table"></div>
        <div id="steps" class="mb3">

            {{-- ✅ 登録済み要素一覧 --}}
            <div id="elements-list" class="mt-5">
                <h5>登録済みの要素一覧</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>プロジェクト名</th>
                            <th>キーワード</th>
                            <th>環境</th>
                            <th>Laravelバージョン</th>
                            <th>登録日時</th>
                        </tr>
                    </thead>
                    <tbody id="elements-table-body">
                        <tr>
                            <td colspan="6">読み込み中...</td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="mb-4">
                <h3>ステップ①：作業フォルダに移動</h3>
                <p>任意の作業フォルダ（例：Laravelなど）にターミナルで移動します。このフォルダ内に新しいプロジェクトが作成されます。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code>cd Laravel</code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ②：Laravelプロジェクト作成</h3>
                <p>Composer を使って、指定したバージョンの Laravel のひな形を新しいディレクトリにダウンロード・展開します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="project-create"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ③：プロジェクトフォルダへ移動</h3>
                <p>作成された新しいプロジェクトディレクトリに移動します。以降のコマンドは全てこのディレクトリ内で実行します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="cd-project"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ④：Git初期化</h3>
                <p>ローカルリポジトリ を作成し、このプロジェクトのファイルのバージョン管理を開始します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code>git init</code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑤：.gitignore確認</h3>
                <p>Gitに含めたくないファイルやフォルダ（機密情報、自動生成物など）がリストされていることを確認します。これで DB_PASSWORD などの流出を防げます。<br>
                    Laravelには最初から以下が含まれています：<br>
                    ・ vendor/<br>
                    ・ .env<br>
                    → gitignoreで除外されているので安全。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑥：GitHubリポジトリ作成</h3>
                <p>GitHub上で、新規のリモートリポジトリを作成します。ローカルのコードをプッシュするアップロード先となります。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="db-project"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑦：GitHubとローカルを接続</h3>
                <p>ローカルリポジトリに、プッシュ先であるリモートリポジトリ（GitHub）のURLをコピーして貼り付けし、接続します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="remote-add"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑧：最初のコミットとプッシュ</h3>
                <p>プロジェクトの全ファイルをステージングし、コミットし、GitHubへ最初のコードをアップロードします。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="first-commit"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑨：環境設定（.env）</h3>
                <p>アプリケーションがデータベースに接続するための情報（DB名、ユーザー名、パスワードなど）を設定ファイル（.env）に記述します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="env-config"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑩：データベース作成</h3>
                <p>MySQLなどのデータベース管理ツール（phpMyAdminなど）で、ステップ⑨で指定した空のデータベースを実際に作成します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="create-db"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑪：マイグレーション実行（標準テーブル）</h3>
                <p>Laravelに標準で用意されている users テーブル などの認証に必要なテーブルを、手順⑩で作成したデータベースに一括で作成します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code>php artisan migrate</code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑫：マイグレーション作成</h3>
                <p>アプリで必要なカスタムテーブルの設計図（マイグレーションファイル）を生成し、カラム構造をコードで定義します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="migration"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑬：マイグレーション実行（自作テーブル）</h3>
                <p>手順⑫で定義したテーブルの設計図を読み込み、データベースに実際のテーブルを作成します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code>php artisan migrate</code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑭：モデル作成</h3>
                <p>データベースのテーブルとやり取りをするための モデル を作成し、データの操作ルール（$fillable など）を定義します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="model"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑮：コントローラ作成</h3>
                <p>ユーザーからのリクエストを受け付け、処理を行い、ビューを返す役割を持つ Controller を作成します。ここでデータを取得し、ビューに渡します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="controller"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑯：ルート設定</h3>
                <p>特定のURLへのアクセスがあったときに、どのコントローラーのどのメソッドを実行するかを定義します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="route"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑰：ビュー呼び出し</h3>
                <p>コントローラーから渡されたデータを受け取り、HTMLとして整形して画面に表示するためのテンプレートファイル（View）を作成します。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="view"></code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステッ⑱：動作確認</h3>
                <p>サーバーを起動し、ブラウザで http://127.0.0.1:8000/ にアクセス。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code>php artisan serve</code></pre>
                </div>
            </div>

            <div class="mb-4">
                <h3>ステップ⑲：GitHubに反映</h3>
                <p>GitHubへ初期設定の終わったコードをアップロードします。</p>
                <div class="code-container">
                    <div class="code-header">
                        💾 コード
                        <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                    </div>
                    <pre class="code-block"><code id="last-commit"></code></pre>
                </div>
            </div>
            <h4>お疲れさまでした。</h4>
            <h4>これで、新規プロジェクト作成〜自作テーブル作成〜GitHub管理までのフローが完結です。</h4>
            <h4>ここからが本番です。頑張りましょう！</h4>
        </div>
        <script src="{{ asset('js/script.js') }}" defer></script>

    </div>
    @endsection