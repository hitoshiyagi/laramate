<?php

namespace App\Http\Controllers;

use App\Models\Element;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElementController extends Controller
{
    // -------------------------------
    // 子要素一覧（Ajax用）
    // -------------------------------
    public function index()
    {
        $userId = Auth::id();
        $elements = Element::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->get();

        return response()->json(['success' => true, 'elements' => $elements]);
    }

    // -------------------------------
    // 子要素作成（通常）
    // -------------------------------
    public function store(Request $request)
    {
        $validated = $this->validateElement($request, true);

        $project = Project::firstOrCreate(
            ['name' => $validated['project_name'], 'user_id' => Auth::id()],
            ['database_name' => $validated['database_name']]
        );

        $element = Element::create(array_merge($validated, [
            'project_id'    => $project->id,
            'database_name' => $project->database_name,
        ]));

        $steps = $this->generateDevSteps($project, $element);

        return response()->json(['success' => true, 'element' => $element, 'steps' => $steps]);
    }

    // -------------------------------
    // 追加要素作成画面
    // -------------------------------
    public function createAdditional($projectId)
    {
        $project = Project::findOrFail($projectId);
        $this->authorizeProject($project);

        return view('elements.create_additional', compact('project'));
    }

    // -------------------------------
    // 追加要素登録
    // -------------------------------
    public function storeAdditional(Request $request)
    {
        $validated = $this->validateElement($request, false);

        $project = Project::findOrFail($validated['project_id']);
        $this->authorizeProject($project);

        Element::create([
            'project_id'      => $project->id,
            'keyword'         => $validated['keyword'],
            'env'             => $validated['env'],
            'laravel_version' => $validated['laravel_version'],
            'table_name'      => $validated['table_name'],
            'model_name'      => $validated['model_name'],
            'controller_name' => $validated['controller_name'],
            'database_name'   => $project->database_name,
        ]);

        return redirect()->route('projects.show', $project->id)
            ->with('success', '子要素を追加しました');
    }

    // -------------------------------
    // Ajax：子要素重複チェック
    // -------------------------------
    public function check(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'project_name' => 'required|string|max:255',
            ]);

            $project = Project::where('name', $request->project_name)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $exists = $project->elements()
                ->where('keyword', $request->name)
                ->exists();

            return response()->json(['exists' => $exists]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['exists' => false]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'サーバーエラー'], 500);
        }
    }

    // -------------------------------
    // 編集画面表示
    // -------------------------------
    public function edit(Element $element)
    {
        $this->authorizeProject($element->project);
        return view('elements.edit', compact('element'));
    }

    // -------------------------------
    // 更新処理
    // -------------------------------
    public function update(Request $request, Element $element)
    {
        $this->authorizeProject($element->project);

        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'table_name' => ['required', 'string', 'max:255'],
            'model_name' => ['required', 'string', 'max:255'],
            'controller_name' => ['required', 'string', 'max:255'],
        ]);

        $element->update($validated);

        return redirect()->route('projects.show', $element->project_id)
            ->with('success', '要素を更新しました');
    }

    // -------------------------------
    // 子要素削除（Ajax）
    // -------------------------------
    public function destroy(Element $element)
    {
        $this->authorizeProject($element->project);

        $element->delete();
        return response()->json(['success' => true, 'message' => '要素を削除しました']);
    }

    // -------------------------------
    // 共通メソッド：バリデーション
    // -------------------------------
    private function validateElement(Request $request, bool $isStore = true)
    {
        $rules = $isStore
            ? [
                'project_name'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
                'database_name'   => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
            ]
            : [
                'project_id'      => ['required', 'integer', 'exists:projects,id'],
            ];

        return $request->validate(array_merge($rules, [
            'keyword'         => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'env'             => ['required', 'string', 'max:50'],
            'laravel_version' => ['required', 'string', 'max:50'],
            'table_name'      => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
            'model_name'      => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'controller_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
        ]));
    }

    // -------------------------------
    // 共通メソッド：権限チェック
    // -------------------------------
    private function authorizeProject(Project $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);
    }

    // -------------------------------
    // 共通メソッド：開発ステップ生成
    // -------------------------------
    private function generateDevSteps(Project $project, Element $element)
    {
        // 'mamp' または 'xampp' 
        if ($element->env === 'mamp') {
            $dbPassword = 'root';
        } elseif ($element->env === 'xampp') {
            $dbPassword = '';
        } else {
            $dbPassword = ''; // デフォルト
        }
        return [
            [
                'title' => 'ステップ①：作業フォルダに移動',
                'description' => "任意の作業フォルダ（例：Laravelなど）にターミナルで移動します。<br><br>■学習ポイント：このフォルダにプロジェクトを作ることで、後から整理しやすくなります。`cd` コマンドの意味は Change Directory（ディレクトリを移動する）です。",
                'command' => 'cd Laravel',
            ],
            [
                'title' => 'ステップ②：Laravelプロジェクト作成',
                'description' => "バージョン「{$element->laravel_version}」のLaravelプロジェクト「{$project->name}」をComposerで作成します。<br><br>■学習ポイント：ComposerはPHPのパッケージ管理ツールです。このコマンドでLaravelの雛形が作成されます。バージョン指定をすることで将来的な互換性を保てます。",
                'command' => "composer create-project laravel/laravel {$project->name} \"{$element->laravel_version}\"",
            ],
            [
                'title' => 'ステップ③：プロジェクトフォルダへ移動',
                'description' => "作成されたプロジェクトディレクトリ「{$project->name}」に移動します。<br><br>■学習ポイント：ここからはプロジェクト内での操作になります。フォルダの中に Laravel のファイル群（app/, routes/ など）があることを確認すると理解が深まります。",
                'command' => "cd {$project->name}",
            ],

            // Git / GitHub設定
            [
                'title' => 'ステップ④：Git初期化',
                'description' => "ローカルリポジトリを作成し、バージョン管理を開始します。<br><br>■学習ポイント：Gitはバージョン管理ツールです。`git init`でプロジェクトを管理下に置きます。",
                'command' => 'git init',
            ],
            [
                'title' => 'ステップ⑤：リモートリポジトリ設定',
                'description' => "GitHub上にプロジェクト名「{$project->name}」でリポジトリを作成し、URLを設定します。（\"ユーザー名\"をご自身のIDに置き換えてください）<br><br>■学習ポイント：GitHub上のリモートリポジトリと連携します。URLのユーザー名部分は自分のものに置き換えます。",
                'command' => "git remote add origin https://github.com/ユーザー名/{$project->name}.git",
            ],
            [
                'title' => 'ステップ⑥：最初のコミットとプッシュ',
                'description' => "初期ファイルをGitHubへアップロードします。<br><br>■学習ポイント：`git add .`で変更をステージング、`git commit`で保存、`git push`でリモートに反映されます。初心者はまず「コミットとは何か」を理解することが重要です。",
                'command' => "git add .\ngit commit -m \"first commit\"\ngit branch -M main\ngit push -u origin main",
            ],

            // データベース関連
            [
                'title' => 'ステップ⑦：環境設定 (.env)',
                'description' => "DB名などを .env ファイルに設定します。<br><br>■学習ポイント：.env ファイルでデータベース接続情報を設定します。Laravel はここで設定された情報をもとに DB と接続します。",
                'command' => "DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE={$project->database_name}\nDB_USERNAME=root\nDB_PASSWORD={$dbPassword}",
            ],
            [
                'title' => 'ステップ⑧：データベース作成',
                'description' => "MySQLなどのツールで、DB名「{$project->database_name}」の空のデータベースを作成します。（手動またはSQL実行）<br><br>■学習ポイント：Laravel のマイグレーションでテーブルを作る前提として、空のデータベースを用意します。",
                'command' => "CREATE DATABASE {$project->database_name}",
            ],
            [
                'title' => 'ステップ⑨：標準テーブルのマイグレーション',
                'description' => "usersテーブルなど、Laravelに標準で用意されているテーブルを作成します。<br><br>■学習ポイント：標準のテーブルが DB に作られたことを確認すると理解が深まります。",
                'command' => 'php artisan migrate',
            ],
            [
                'title' => 'ステップ⑩：カスタムマイグレーションファイル作成',
                'description' => "テーブル「{$element->table_name}」の設計図となるマイグレーションファイルを生成します。<br><br>■学習ポイント：マイグレーションは「テーブル構造の変更履歴」を管理する仕組みです。",
                'command' => "php artisan make:migration create_{$element->table_name}_table --create={$element->table_name}",
            ],
            [
                'title' => 'ステップ⑪：カスタムテーブルのマイグレーション実行',
                'description' => "手順⑩で定義したテーブルをデータベースに作成します。<br><br>■学習ポイント：`php artisan migrate:status` で状態を確認できます。",
                'command' => 'php artisan migrate',
            ],

            // アプリケーション骨格作成
            [
                'title' => 'ステップ⑫：モデル作成',
                'description' => "{$element->model_name} モデルを作成します。<br><br>■学習ポイント：Eloquent ORM のモデルは DB テーブルとやり取りする「窓口」です。",
                'command' => "php artisan make:model {$element->model_name}",
            ],
            [
                'title' => 'ステップ⑬：コントローラ作成',
                'description' => "{$element->controller_name} コントローラを作成します。<br><br>■学習ポイント：コントローラは画面とモデルの橋渡しを担当します。CRUD 操作（一覧・詳細・作成・更新・削除）を処理します。",
                'command' => "php artisan make:controller {$element->controller_name}",
            ],

            // ルート設定とサーバー起動
            [
                'title' => 'ステップ⑭：ルート設定 (web.php)',
                'description' => "リソースルートを設定します。<br><br>■学習ポイント：URL とコントローラを紐付けます。`/{$element->table_name}` にアクセスしたら {$element->controller_name} が呼ばれる仕組みです。",
                'command' => "Route::resource('/{$element->table_name}', App\\Http\\Controllers\\{$element->controller_name}::class);",
            ],
            [
                'title' => 'ステップ⑮：ビュー作成',
                'description' => "コントローラから呼び出すビューファイルを作成します。<br><br>■学習ポイント：Blade テンプレートで画面を作成し、コントローラで渡したデータを表示する練習になります。",
                'command' => "resources/views/{$element->table_name}/index.blade.php などを作成",
            ],
            [
                'title' => 'ステップ⑯：サーバー起動',
                'description' => "開発サーバーを起動し、ブラウザで動作確認を行います。<br><br>■学習ポイント：`http://127.0.0.1:8000` にアクセスして画面が表示されるか確認します。",
                'command' => "php artisan serve",
            ],
        ];
    }
}
