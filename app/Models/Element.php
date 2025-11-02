<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    // 代入可能なカラムを指定
    protected $fillable = [
        'project_id',
        'name',
        'db',
        'model',
        'table',
    ];

    // 🔸 要素群は一つのプロジェクトに属する
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
