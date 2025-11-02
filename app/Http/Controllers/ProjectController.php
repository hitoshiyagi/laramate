<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
        // 一覧表示
    public function index()
    {
        // ログインユーザーのプロジェクト一覧を取得
        $projects = Project::where('user_id', Auth::id())->get();

        return view('projects.index', compact('projects'));
    }
    public function show(Project $project)
    {
        $elements = $project->elements; // 紐づく要素を取得
        return view('projects.show', compact('project', 'elements'));
    }
}
