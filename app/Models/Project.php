<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // 一括代入を許可するカラムを指定
    protected $fillable = [
        'name'
    ];

    // 🔸 一つのプロジェクトは複数の要素群（elements）を持つ
    public function elements()
    {
        return $this->hasMany(Element::class);
    }
}
