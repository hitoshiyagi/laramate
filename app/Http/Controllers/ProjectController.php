<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // プロジェクト作成
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // 🔹 ログイン中のユーザー情報を付けて保存
        $project = Project::create([
            'name' => $validated['name'],
            'user_id' => Auth::id(), // ← ここを追加
        ]);

        return response()->json([
            'success' => true,
            'project' => $project
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
