<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use App\Models\Project;

class ElementController extends Controller
{
    /**
     * 要素をDBに登録
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'project_name'      => 'required|string|max:255',
            'keyword'           => 'required|string|max:255',
            'env'               => 'required|string|max:50',
            'laravel_version'   => 'required|string|max:50',
            'table_name'        => 'required|string|max:255',
            'model_name'        => 'required|string|max:255',
            'controller_name'   => 'required|string|max:255',
            'db_name'           => 'required|string|max:255',
        ]);

        // プロジェクト取得または作成
        $project = Project::firstOrCreate(
            ['name' => $validated['project_name']],
            ['user_id' => auth()->id()]
        );

        // 要素を作成
        $element = Element::create([
            'project_id'        => $project->id,
            'keyword'           => $validated['keyword'],
            'env'               => $validated['env'],
            'laravel_version'   => $validated['laravel_version'],
            'table_name'        => $validated['table_name'],
            'model_name'        => $validated['model_name'],
            'controller_name'   => $validated['controller_name'],
            'db_name'           => $validated['db_name'],
        ]);

        // 登録後の手順例
        $steps = [
            [
                'title' => 'ステップ①：作業フォルダに移動',
                'description' => '任意の作業フォルダにターミナルで移動します。',
                'command' => 'cd Laravel'
            ],
            [
                'title' => 'ステップ②：Laravelインストール',
                'description' => '指定バージョンのLaravelをインストールします。',
                'command' => "composer create-project laravel/laravel {$project->name} \"{$element->laravel_version}\""
            ],
            [
                'title' => 'ステップ③：モデル作成',
                'description' => "{$element->model_name} モデルを作成します。",
                'command' => "php artisan make:model {$element->model_name}"
            ],
            [
                'title' => 'ステップ④：コントローラ作成',
                'description' => "{$element->controller_name} コントローラを作成します。",
                'command' => "php artisan make:controller {$element->controller_name}"
            ],
            [
                'title' => 'ステップ⑤：ビュー作成',
                'description' => "{$element->model_name} ビューを作成します。",
                'command' => null
            ]
        ];

        return response()->json([
            'success' => true,
            'element' => $element,
            'steps' => $steps
        ]);
    }

    /**
     * 要素一覧取得
     */
    public function index()
    {
        $elements = Element::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'elements' => $elements,
        ]);
    }
}
