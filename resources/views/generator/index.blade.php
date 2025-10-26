<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>LaraMate クラス名ジェネレータ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #f8f9fa;
            margin: 30px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .keyword-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* フォームの青い枠を削除する修正 */
        .form-control:focus,
        .form-select:focus,
        .btn:focus {
            box-shadow: none !important;
            border-color: inherit !important;
        }

        .code-block {
            position: relative;
            background: #454545;
            color: #efefef;
            font-family: "Fira Code", monospace;
            border-radius: 0px;
            margin-bottom: 60px;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            padding: 15px;
            overflow-x: auto;
        }

        .code-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #454545;
            color: #efefef;
            padding: 8px 12px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            font-size: 0.9rem;
        }

        .copy-btn {
            color: #efefef;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        #steps {
            display: none;
            animation: fadeIn 0.6s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="container">

    <h1>LaraMate クラス名ジェネレータ</h1>
    <p>キーワードを入力すると、モデル・テーブル・コントローラ・ビュー・DB名を自動生成し、下の手順に反映します。</p>

    <div class="keyword-box mb-4">
        <div class="input-group">
            <span class="input-group-text">キーワード</span>
            <input type="text" id="keyword" class="form-control" placeholder="例：memberlist">

            <select id="env-select" class="form-select" style="max-width: 150px;">
                <option value="" disabled selected>開発環境</option>
                <option value="xampp">XAMPP</option>
                <option value="mamp">MAMP</option>
            </select>

            <select id="laravel-version" class="form-select" style="max-width: 120px;">
                <option value="">Laravel</option>
                <option value="10.*">10系</option>
                <option value="11.*">11系</option>
                <option value="12.*">12系</option>
            </select>

            <button class="btn btn-primary" onclick="generate()">生成</button>
        </div>
    </div>

    <div id="result-table"></div>

    <hr>

    <div id="steps" class="mt-4">

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
                <pre class="code-block"><code id="env-config"></code></pre>
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
                <pre class="code-block"><code id="env-config"></code></pre>
            </div>
        </div>

        <div class="mb-4">
            <h3>ステップ⑫：マイグレーション作成</h3>
            <p>アプリで必要なカスタムテーブル（members）の設計図（マイグレーションファイル）を生成し、カラム構造をコードで定義します。</p>
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
            <p>手順⑫で定義した members テーブルの設計図を読み込み、データベースに実際のテーブルを作成します。</p>
            <div class="code-container">
                <div class="code-header">
                    💾 コード
                    <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                </div>
                <pre class="code-block"><code id="migration"></code></pre>
            </div>
        </div>


        <div class="mb-4">
            <h3>ステップ⑭：モデル作成</h3>
            <p>データベースの members テーブルとやり取りをするための Member モデル を作成し、データの操作ルール（$fillable など）を定義します。</p>
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
            <p>ユーザーからのリクエストを受け付け、処理を行い、ビューを返す役割を持つ MemberController を作成します。ここでデータを取得し、ビューに渡します。</p>
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
            <p>特定のURL（/members）へのアクセスがあったときに、どのコントローラーのどのメソッド（MemberController の index）を実行するかを定義します。</p>
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
            <p>コントローラーから渡されたデータ（$members）を受け取り、HTMLとして整形して画面に表示するためのテンプレートファイル（View）を作成します。</p>
            <div class="code-container">
                <div class="code-header">
                    💾 コード
                    <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                </div>
                <pre class="code-block"><code id="view"></code></pre>
            </div>
        </div>
    </div>

    <script>
        // Enterキーで生成
        document.getElementById('keyword').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') generate();
        });

        function generate() {
            const keyword = document.getElementById('keyword').value.trim();
            const envSelect = document.getElementById('env-select');
            const env = envSelect.value;
            const laravelVersion = document.getElementById('laravel-version').value;

            // 環境選択の初期値が選択されている場合は警告
            if (!keyword || !env) {
                alert('キーワードと開発環境を選択してください');
                return;
            }

            const Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
            const Table = keyword.toLowerCase() + 's';
            const Controller = Model + 'Controller';
            const DB = keyword.toLowerCase() + '_db';
            const Repo = keyword.toLowerCase() + '-app';

            // DBユーザー・パスワードを環境で切り替え
            let dbUser = "root";
            let dbPass = ""; // XAMPPをデフォルトに設定
            if (env === "mamp") {
                dbPass = "root";
            } else if (env === "xampp") {
                dbPass = "";
            }

            // Laravelバージョンによるコマンド切替
            let projectCommand = `composer create-project laravel/laravel ${Repo}`;
            if (laravelVersion) {
                projectCommand = `composer create-project "laravel/laravel=${laravelVersion}" ${Repo}`;
            }

            // 結果テーブル
            const tableHTML = `
            <table class="table table-bordered table-striped mt-3">
              <thead class="table-light">
                <tr><th>項目</th><th>生成結果</th></tr>
              </thead>
              <tbody>
                <tr><td>プロジェクト名</td><td>${Repo}</td></tr>
                <tr><td>GitHubリポジトリ名</td><td>${Repo}</td></tr>
                <tr><td>DB名</td><td>${DB}</td></tr>
                <tr><td>モデル名</td><td>${Model}</td></tr>
                <tr><td>テーブル名</td><td>${Table}</td></tr>
                <tr><td>コントローラ名</td><td>${Controller}</td></tr>
                <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
              </tbody>
            </table>`;
            document.getElementById('result-table').innerHTML = tableHTML;

            // ステップの表示
            document.getElementById('project-create').innerText = projectCommand;
            document.getElementById('cd-project').innerText = `cd ${Repo}`;
            document.getElementById('db-project').innerText = `${Repo}`;
            document.getElementById('remote-add').innerText =
                `git remote add origin https://github.com/ユーザー名/${Repo}.git`;
            document.getElementById('first-commit').innerText =
                `git add .\ngit commit -m "first commit"\ngit branch -M main\ngit push -u origin main`;
            document.getElementById('env-config').innerText =
                `DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=${DB}\nDB_USERNAME=${dbUser}\nDB_PASSWORD=${dbPass}`;
            document.getElementById('migration').innerText =
                `php artisan make:migration create_${Table}_table --create=${Table}`;
            document.getElementById('model').innerText = `php artisan make:model ${Model}`;
            document.getElementById('controller').innerText = `php artisan make:controller ${Controller}`;
            document.getElementById('route').innerText =
                `Route::get('/${Table}', [App\\Http\\Controllers\\${Controller}::class, 'index'])->name('${Table}.index');`;
            document.getElementById('view').innerText =
                `return view('${Table}/index', compact('${Table}'));`;

            document.getElementById('steps').style.display = "block";
        }

        function copyCode(button) {
            const code = button.closest('.code-container').querySelector('code').innerText;
            navigator.clipboard.writeText(code);
            button.innerText = '✅ コピー済み';
            setTimeout(() => button.innerText = '📋 コピー', 1500);
        }
    </script>
</body>

</html>