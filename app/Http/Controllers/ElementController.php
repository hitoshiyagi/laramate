<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use App\Models\Project;

class ElementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'keyword' => 'required|string|max:255',
            'env' => 'required|string',
            'laravel_version' => 'required|string'
        ]);

        $project = Project::where('name', $validated['project_name'])->first();
        if (!$project) {
            return response()->json(['success' => false, 'message' => '指定されたプロジェクトが存在しません。']);
        }

        $element = Element::create([
            'project_id' => $project->id,
            'keyword' => $validated['keyword'],
            'env' => $validated['env'],
            'laravel_version' => $validated['laravel_version'],
        ]);

        // 手順データの例
        $steps = [
            [
                'title' => 'ステップ①：作業フォルダに移動',
                'description' => '任意の作業フォルダ（例：Laravelなど）にターミナルで移動します。',
                'path' => '/Users/you/Projects',
                'elementName' => '',
                'command' => 'cd Laravel'
            ],
            [
                'title' => 'ステップ②：Laravelインストール',
                'description' => 'Laravelの指定バージョンをインストールします。',
                'path' => '',
                'elementName' => '',
                'command' => "composer create-project laravel/laravel {$project->name} \"{$element->laravel_version}\""
            ],
            [
                'title' => 'ステップ③：モデル作成',
                'description' => "{$element->keyword} モデルを作成します。",
                'path' => 'app/Models',
                'elementName' => $element->keyword,
                'command' => null
            ],
            [
                'title' => 'ステップ④：コントローラ作成',
                'description' => "{$element->keyword} コントローラを作成します。",
                'path' => 'app/Http/Controllers',
                'elementName' => $element->keyword,
                'command' => null
            ],
            [
                'title' => 'ステップ⑤：ビュー作成',
                'description' => "{$element->keyword} ビューを作成します。",
                'path' => 'resources/views/' . strtolower($element->keyword),
                'elementName' => $element->keyword,
                'command' => null
            ]
        ];

        return response()->json([
            'success' => true,
            'element' => $element,
            'steps' => $steps
        ]);
    }

    // 要素一覧取得
    public function index()
    {
        $elements = Element::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'elements' => $elements,
        ]);
    }


}

