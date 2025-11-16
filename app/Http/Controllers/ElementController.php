<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Element;
use App\Models\Project;

class ElementController extends Controller
{
    /**
     * 子要素一覧取得（ログインユーザーのみ）
     */
    public function index()
    {
        $userId = Auth::id();

        $elements = Element::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'elements' => $elements,
        ]);
    }

    /**
     * 子要素作成
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'project_name'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'keyword'         => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'env'             => ['required', 'string', 'max:50'],
            'laravel_version' => ['required', 'string', 'max:50'],
            'table_name'      => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
            'model_name'      => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'controller_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'database_name'   => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
        ]);

        $userId = Auth::id();

    // プロジェクトが存在しなければ作成
        $project = Project::firstOrCreate(
            ['name' => $validated['project_name'], 'user_id' => $userId],
            ['database_name' => $validated['database_name']]
        );

    // 子要素作成
        $element = Element::create([
            'project_id'      => $project->id,
            'keyword'         => $validated['keyword'],
            'env'             => $validated['env'],
            'laravel_version' => $validated['laravel_version'],
            'table_name'      => $validated['table_name'],
            'model_name'      => $validated['model_name'],
            'controller_name' => $validated['controller_name'],
            'database_name'   => $validated['database_name'],
        ]);

    // 開発ステップ例
        $steps = [
            [
                'title' => 'ステップ①：作業フォルダに移動',
                'description' => '任意の作業フォルダに移動します。',
                'command' => 'cd Laravel',
            ],
            [
                'title' => 'ステップ②：Laravelインストール',
                'description' => '指定バージョンのLaravelをインストールします。',
                'command' => "composer create-project laravel/laravel {$project->name} \"{$element->laravel_version}\"",
            ],
            [
                'title' => 'ステップ③：モデル作成',
                'description' => "{$element->model_name} モデルを作成します。",
                'command' => "php artisan make:model {$element->model_name}",
            ],
            [
                'title' => 'ステップ④：コントローラ作成',
                'description' => "{$element->controller_name} コントローラを作成します。",
                'command' => "php artisan make:controller {$element->controller_name}",
            ],
            [
                'title' => 'ステップ⑤：ビュー作成',
                'description' => "ビューを作成します。",
                'command' => null,
            ],
        ];

        return response()->json([
            'success' => true,
            'element' => $element,
            'steps'   => $steps,
        ]);
    }

    // 編集画面表示
    public function edit(Element $element)
    {
        if ($element->project->user_id !== Auth::id()) {
            abort(403);
        }

        return view('elements.edit', compact('element')); // ← view名を edit に
    }

    // 更新処理
    public function update(Request $request, Element $element)
    {
        if ($element->project->user_id !== Auth::id()) {
            abort(403);
        }

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

    /**
     * 子要素削除
     */
    public function destroy(Element $element)
    {
        // 権限チェック
        if ($element->project->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => '権限がありません',
            ], 403);
        }

        $element->delete();

        return response()->json([
            'success' => true,
            'message' => '要素を削除しました',
        ]);
    }
}
