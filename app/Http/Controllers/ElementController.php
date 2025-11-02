<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Element;
use Illuminate\Http\Request;

class ElementController extends Controller
{
   // 要素郡作成フォーム
   public function create(Project $project)
   {
    return view('elements.create', compact('project'));
   }

    // 要素郡登録処理 
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate(([
            'name' => 'required|string|max:255',
            'db' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'table' => 'nullable|string|max:255',
        ]));
$project->elements()->create($validated);

return redirect()->route('projects.show', $project)
->with('success','要A素名を追加しました！');

    }

}
