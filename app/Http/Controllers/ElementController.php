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

        // プロジェクト名からIDを取得（なければエラー返却）
        $project = Project::where('name', $validated['project_name'])->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => '指定されたプロジェクトが存在しません。'
            ]);
        }

        // 🔸 project_id で要素群を紐付け
        $element = Element::create([
            'project_id' => $project->id,
            'keyword' => $validated['keyword'],
            'env' => $validated['env'],
            'laravel_version' => $validated['laravel_version'],
        ]);

        // 🔸 初学者向け手順
        $steps = [
            "プロジェクトフォルダ '{$project->name}' を作成",
            "Laravel {$element->laravel_version} をインストール",
            "{$element->keyword} モデルを作成",
            "{$element->keyword} コントローラを作成",
            "{$element->keyword} ビューを作成"
        ];

        return response()->json([
            'success' => true,
            'element' => $element,
            'steps' => $steps
        ]);
    }
}
