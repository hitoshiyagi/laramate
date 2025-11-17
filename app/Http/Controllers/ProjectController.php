<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Element;
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

        $project = Project::create([
            'name'          => $validated['name'],
            'repo'          => $validated['name'],
            'database_name' => strtolower($validated['name']) . '_db',
            'user_id'       => $userId,
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    // プロジェクト詳細
    public function show(Project $project)
    {
        $this->authorizeProject($project);

        $elements = $project->elements()->latest()->get();
        return view('projects.show', compact('project', 'elements'));
    }

    // プロジェクト削除
    public function destroy(Project $project)
    {
        $this->authorizeProject($project);

        // 子要素も削除
        $project->elements()->delete();
        $project->delete();

        return response()->json(['success' => true, 'message' => 'プロジェクトを削除しました']);
    }

    // プロジェクト名重複チェック（Ajax用）
    public function checkName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $exists = Project::where('user_id', Auth::id())
            ->where('name', $request->name)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    // 権限チェック共通化
    private function authorizeProject(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
