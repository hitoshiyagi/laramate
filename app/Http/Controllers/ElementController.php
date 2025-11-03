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
            return response()->json([
                'success' => false,
                'message' => '指定されたプロジェクトが存在しません。'
            ]);
        }

        $element = Element::create([
            'project_id' => $project->id,
            'keyword' => $validated['keyword'],
            'env' => $validated['env'],
            'laravel_version' => $validated['laravel_version'],
        ]);

        $steps = [
            "プロジェクトフォルダ '{$project->name}' を作成",
            "Laravel {$element->laravel_version} をインストール",
            "{$element->keyword} モデルを作成",
            "{$element->keyword} コントローラを作成",
            "{$element->keyword} ビューを作成"
        ];

        return response()->json([
            'success' => true,
            'element' => [
                'id' => $element->id,
                'project_name' => $project->name,
                'repository' => $project->repository, // ここでリポジトリ名も返す
                'keyword' => $element->keyword,
                'env' => $element->env,
                'laravel_version' => $element->laravel_version
            ],
            'steps' => $steps
        ]);
    }

    public function index()
    {
        // リレーションを読み込んでプロジェクト名とリポジトリ名も取得
        $elements = Element::with('project')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'elements' => $elements->map(function ($el) {
                return [
                    'id' => $el->id,
                    'project_name' => $el->project->name,
                    'repository' => $el->project->repository,
                    'keyword' => $el->keyword,
                    'env' => $el->env,
                    'laravel_version' => $el->laravel_version,
                    'created_at' => $el->created_at->format('Y-m-d H:i')
                ];
            })
        ]);
    }
}
