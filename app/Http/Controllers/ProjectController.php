<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // プロジェクト一覧
    public function index()
    {
        $projects = Auth::user()->projects()->latest()->get();
        return view('projects.index', compact('projects'));
    }

    // プロジェクト作成画面
    public function create()
    {
        return view('projects.create');
    }

    // プロジェクト保存
    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(fn($q) => $q->where('user_id', $userId))
            ],
        ]);

        $dbName = strtolower($validated['name']) . '_db';

        $project = Project::create([
            'name' => $validated['name'],
            'repo' => $validated['name'],
            'database_name' => $dbName,
            'user_id' => $userId,
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    // プロジェクト詳細
    public function show(Project $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);

        $elements = $project->elements()->latest()->get();
        return view('projects.show', compact('project', 'elements'));
    }

    // プロジェクト削除
    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => '権限がありません'], 403);
        }

        // 子要素も削除
        $project->elements()->delete();
        $project->delete();

        return response()->json(['success' => true, 'message' => 'プロジェクトを削除しました']);
    }

    // プロジェクト名重複チェック（Ajax用）
    public function checkName(Request $request)
    {
        $userId = Auth::id();
        $request->validate(['name' => 'required|string|max:255']);

        $exists = Project::where('user_id', $userId)
            ->where('name', $request->name)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
