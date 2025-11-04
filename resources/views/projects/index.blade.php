@extends('adminlte::page')

@section('title', '新規プロジェクト作成')

@section('content_header')
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stop

@section('content')
<div class="container" data-page="welcome">
    <div class="row justify-content-center">
        <div class="col-md-10">

            {{-- プロジェクト作成カード --}}
            <div class="card mt-4" id="project-card">
                <div class="card-header">プロジェクト作成</div>
                <div class="card-body">
                    <p>プロジェクト名を入力し、新規プロジェクトを作成します。</p>

                    <div class="row mb-3">
                        <label for="name" class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                        <div class="col-md-6">
                            <input type="text" name="name" id="name" class="form-control" placeholder="例：laramate" required>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-primary" id="create-project-btn">要素名の生成へ進む</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 要素名生成カード --}}
            <div class="card mt-4" id="element-card" style="display:none;">
                <div class="card-header">要素名生成</div>
                <div class="card-body">
                    <form id="element-form">
                        {{-- プロジェクト名表示 --}}
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">プロジェクト名</label>
                            <div class="col-md-6">
                                <input type="text" id="element-project-name" class="form-control" readonly>
                            </div>
                        </div>

                        {{-- 要素名キーワード --}}
                        <div class="row mb-3">
                            <label for="keyword" class="col-md-4 col-form-label text-md-end">要素名キーワード</label>
                            <div class="col-md-6">
                                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="例：member" required>
                            </div>
                        </div>

                        {{-- 開発環境 --}}
                        <div class="row mb-3">
                            <label for="env-select" class="col-md-4 col-form-label text-md-end">開発環境</label>
                            <div class="col-md-6">
                                <select name="env" id="env-select" class="form-select" required>
                                    <option value="" disabled selected>選択してください</option>
                                    <option value="xampp">XAMPP</option>
                                    <option value="mamp">MAMP</option>
                                </select>
                            </div>
                        </div>

                        {{-- Laravel バージョン --}}
                        <div class="row mb-3">
                            <label for="laravel-version" class="col-md-4 col-form-label text-md-end">Laravelバージョン</label>
                            <div class="col-md-6">
                                <select name="laravel_version" id="laravel-version" class="form-select" required>
                                    <option value="">選択してください</option>
                                    <option value="10.*">10系</option>
                                    <option value="11.*">11系</option>
                                    <option value="12.*">12系</option>
                                </select>
                            </div>
                        </div>

                        {{-- 生成ボタン --}}
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-success" id="preview-elements">要素名を生成する</button>
                            </div>
                        </div>
                    </form>

                    {{-- 生成結果プレビュー --}}
                    <div id="generation-result" class="mt-4" style="display:none;">
                        <div id="result-table" class="bg-light"></div>

                        <div class="mt-3 col-md-6 offset-md-4">
                            <button class="btn btn-primary" id="register-elements">登録する</button>
                            <button class="btn btn-secondary" id="clear-elements">クリア</button>
                            <div id="generation-message" class="mt-2 text-success" style="display:none;"></div>

                        </div>
                        <div id="generation-steps-area" style="display:none;" class="mt-4">
                            <h5>登録後の手順</h5>
                            <div id="generation-steps" class="d-flex flex-column gap-3 mb-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

{{-- CSRF トークン --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('js')
<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/project.js') }}"></script>
<script src="{{ asset('js/element.js') }}"></script>
@stop
@stop