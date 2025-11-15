<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Element;
use App\Models\Project;

class ElementController extends Controller
{
    /**
     * 要素一覧
     * GET /elements
     */
    public function index()
    {
        $userId = Auth::id();

        $elements = Element::whereHas('project', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'elements' => $elements,
        ]);
    }

    /**
     * 要素登録
     * POST /elements
     */
    public function store(Request $request)
    {
        // 🔹 バリデーション
        $validated = $request->validate([
            'project_name'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'keyword'         => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'env'             => ['required', 'string', 'max:50'],
            'laravel_version' => ['required', 'string', 'max:50'],
            'table_name'      => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
            'model_name'      => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'controller_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'db_name'         => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
        ]);

        $userId = Auth::id();

        // 🔹 プロジェクトを取得 or 作成
        $project = Project::firstOrCreate(
            ['name' => $validated['project_name'], 'user_id' => $userId]
        );

        // 🔹 要素作成
        $element = Element::create([
            'project_id'      => $project->id,
            'keyword'         => $validated['keyword'],
            'env'             => $validated['env'],
            'laravel_version' => $validated['laravel_version'],
            'table_name'      => $validated['table_name'],
            'model_name'      => $validated['model_name'],
            'controller_name' => $validated['controller_name'],
            'db_name'         => $validated['db_name'],
        ]);

        // 🔹 生成手順
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

    /**
     * 要素削除
     * DELETE /elements/{element}
     */
    public function destroy(Element $element)
    {
        // 🔹 権限チェック
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
