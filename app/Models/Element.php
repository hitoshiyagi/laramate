<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'keyword',
        'env',
        'laravel_version',
        'table_name',
        'model_name',
        'controller_name',
        'db_name',
        'repo_name',
    ];

    // 🔸 要素群は1つのプロジェクトに属する
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
