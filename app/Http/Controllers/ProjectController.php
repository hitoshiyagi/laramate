<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // プロジェクト作成
    public function store(Request $request)
    {
        $userId = Auth::id();

        // バリデーション（ユーザー内でプロジェクト名が重複していないか）
        $validator = \Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(fn($query) => $query->where('user_id', $userId)),
            ],
        ], [
            'name.required' => 'プロジェクト名を入力してください。',
            'name.unique' => 'このプロジェクトはすでに存在します。既存プロジェクトに要素を追加してください。',
        ]);

        if ($validator->fails()) {
            // バリデーションエラーを JSON で返す
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // プロジェクト作成
        $project = Project::create([
            'name' => $request->name,
            'repo' => $request->name, // 名前を自動で設定
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'project' => $project,
        ]);
    }

    // 作成画面
    public function create()
    {
        return view('projects.index'); // プロジェクト作成画面
    }

    // プロジェクト一覧
    public function list()
    {
        $user = auth()->user();
        $projects = $user->projects()->latest()->get();

        return view('projects.list', compact('projects'));
    }

    // 一覧（旧index）
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())->get();
        return view('projects.index', compact('projects'));
    }

    // 詳細表示
    public function show(Project $project)
    {
        $project->load('elements');
        return view('projects.show', compact('project'));
    }
}
