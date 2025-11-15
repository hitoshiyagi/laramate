<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // 一覧表示
    public function index()
    {
        $projects = Auth::user()->projects()->latest()->get();
        return view('projects.index', compact('projects'));
    }

    // 作成画面
    public function create()
    {
        return view('projects.create');
    }

    // プロジェクト作成（Ajax）
    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('projects')->where(fn($q) => $q->where('user_id', $userId))],
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'repo' => $validated['name'],
            'user_id' => $userId,
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    // 詳細表示
    public function show(Project $project)
    {
        if ($project->user_id !== Auth::id()) abort(403);
        $elements = $project->elements()->latest()->get();
        return view('projects.show', compact('project', 'elements'));
    }

    // 削除（Ajax）
    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => '権限がありません'], 403);
        }

        $project->elements()->delete();
        $project->delete();

        return response()->json(['success' => true]);
    }
}
