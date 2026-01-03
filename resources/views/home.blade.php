@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('css')
@vite('resources/css/app.css')
{{-- 必要なら追加のカスタムCSS --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('content')
<h1>Welcome to this Laramate.</h1>

{{-- プロジェクト作成カード --}}
<div class="col-md-10">
    <div class="card mt-10" id="project-card">
        <div class="card-header bg-primary">Laramate（ララメイト）とは？</div>
        <div class="card-body">
            <p>
                Laramateは、Laravel開発における「命名の迷い」をなくすためのサポートアプリです。<br>
                プロジェクトや要素名を自動生成し、統一された命名規則でコードの可読性とチームの生産性を高めます。<br><br>
                このアプリは、「命名作業に時間を取られる」「命名ルールがバラバラで混乱する」といった課題を解決したいという想いから生まれました。<br>
                自分自身の課題を出発点に、同じ悩みを持つ開発者の力になれるように――<br>
                Laramateはそんな想いで作られた、開発者の“相棒（mate）”です。
            </p>
        </div>
    </div>
</div>
@stop

@section('js')
@vite('resources/js/element.js')
@vite('resources/js/project.js')
@stop