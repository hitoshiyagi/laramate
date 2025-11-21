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

        $dbName = strtolower($validated['name']) . '_db';

        $project = Project::create([
            'name' => $validated['name'],
            'repo' => $validated['name'],
            'database_name' => $dbName,
            'user_id' => $userId,
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    public function storeAdditional(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'project_id'       => ['required', 'exists:projects,id'],
            'keyword'          => ['required', 'alpha_num'],
            'env'              => ['required', 'in:xampp,mamp'],
            'laravel_version'  => ['required', 'in:10.*,11.*,12.*'],
            'table_name'       => ['required', 'string'],
            'model_name'       => ['required', 'string'],
            'controller_name'  => ['required', 'string'],
        ]);

        // 同一プロジェクト内で keyword の重複チェック
        $exists = Element::where('project_id', $request->project_id)
            ->where('keyword', $request->keyword)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['keyword' => 'このキーワードは既に登録されています'])
                ->withInput();
        }

        // 保存
        Element::create([
            'project_id'      => $request->project_id,
            'keyword'         => $request->keyword,
            'env'             => $request->env,
            'laravel_version' => $request->laravel_version,
            'table_name'      => $request->table_name,
            'model_name'      => $request->model_name,
            'controller_name' => $request->controller_name,
        ]);

        // プロジェクト詳細へ戻る
        return redirect()
            ->route('projects.show', $request->project_id)
            ->with('success', '子要素を追加しました');
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
