// // Enterキーで生成
// document.getElementById("keyword").addEventListener("keydown", function (e) {
//     if (e.key === "Enter") generate();
// });

// function generate() {
//     const keyword = document.getElementById("keyword").value.trim();
//     const envSelect = document.getElementById("env-select");
//     const env = envSelect.value;
//     const laravelVersion = document.getElementById("laravel-version").value;

//     // 環境選択の初期値が選択されている場合は警告
//     if (!keyword || !env) {
//         alert("キーワードと開発環境を選択してください");
//         return;
//     }

//     const Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
//     const Table = keyword.toLowerCase() + "s";
//     const Controller = Model + "Controller";
//     const DB = keyword.toLowerCase() + "_db";
//     const Repo = keyword.toLowerCase() + "-app";

//     // DBユーザー・パスワードを環境で切り替え
//     let dbUser = "root";
//     let dbPass = ""; // XAMPPをデフォルトに設定
//     if (env === "mamp") {
//         dbPass = "root";
//     } else if (env === "xampp") {
//         dbPass = "";
//     }

//     // Laravelバージョンによるコマンド切替
//     let projectCommand = `composer create-project laravel/laravel ${Repo}`;
//     if (laravelVersion) {
//         projectCommand = `composer create-project "laravel/laravel=${laravelVersion}" ${Repo}`;
//     }

//     // 結果テーブル
//     const tableHTML = `
//             <table class="table table-bordered table-striped mt-3">
//               <thead class="table-light">
//                 <tr><th>項目</th><th>生成結果</th></tr>
//               </thead>
//               <tbody>
//                 <tr><td>プロジェクト名</td><td>${Repo}</td></tr>
//                 <tr><td>GitHubリポジトリ名</td><td>${Repo}</td></tr>
//                 <tr><td>DB名</td><td>${DB}</td></tr>
//                 <tr><td>モデル名</td><td>${Model}</td></tr>
//                 <tr><td>テーブル名</td><td>${Table}</td></tr>
//                 <tr><td>コントローラ名</td><td>${Controller}</td></tr>
//                 <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
//               </tbody>
//             </table>`;
//     document.getElementById("result-table").innerHTML = tableHTML;

//     // ステップの表示
//     document.getElementById("project-create").innerText = projectCommand;
//     document.getElementById("cd-project").innerText = `cd ${Repo}`;
//     document.getElementById("db-project").innerText = `${Repo}`;
//     document.getElementById(
//         "remote-add"
//     ).innerText = `git remote add origin https://github.com/ユーザー名/${Repo}.git`;
//     document.getElementById(
//         "first-commit"
//     ).innerText = `git add .\ngit commit -m "first commit"\ngit branch -M main\ngit push -u origin main`;
//     document.getElementById(
//         "env-config"
//     ).innerText = `DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=${DB}\nDB_USERNAME=${dbUser}\nDB_PASSWORD=${dbPass}`;
//     document.getElementById("create-db").innerText = `CREATE DATABASE ${DB}`;
//     document.getElementById(
//         "migration"
//     ).innerText = `php artisan make:migration create_${Table}_table --create=${Table}`;
//     document.getElementById(
//         "model"
//     ).innerText = `php artisan make:model ${Model}`;
//     document.getElementById(
//         "controller"
//     ).innerText = `php artisan make:controller ${Controller}`;
//     document.getElementById(
//         "route"
//     ).innerText = `Route::get('/${Table}', [App\\Http\\Controllers\\${Controller}::class, 'index'])->name('${Table}.index');`;
//     document.getElementById(
//         "view"
//     ).innerText = `return view('${Table}/index', compact('${Table}'));`;
//     document.getElementById(
//         "last-commit"
//     ).innerText = `git add .\ngit commit -m "初期設定完了"\ngit push origin main`;

//     document.getElementById("steps").style.display = "block";
// }

// function copyCode(button) {
//     const code = button
//         .closest(".code-container")
//         .querySelector("code").innerText;
//     navigator.clipboard.writeText(code);
//     button.innerText = "✅ コピー済み";
//     setTimeout(() => (button.innerText = "📋 コピー"), 1500);
// }


// 英単語を複数形に変換する関数（簡易版）
function pluralize(word) {
    word = word.toLowerCase();
    if (word.endsWith("y")) {
        return word.slice(0, -1) + "ies"; // city → cities
    } else if (word.endsWith("s") || word.endsWith("x") || word.endsWith("z") || word.endsWith("ch") || word.endsWith("sh")) {
        return word + "es"; // bus → buses, box → boxes
    } else {
        return word + "s"; // cat → cats
    }
}

// Enterキーで生成
document.getElementById("keyword").addEventListener("keydown", function (e) {
    if (e.key === "Enter") generate();
});

function generate() {
    const keyword = document.getElementById("keyword").value.trim();
    const envSelect = document.getElementById("env-select");
    const env = envSelect.value;
    const laravelVersion = document.getElementById("laravel-version").value;

    if (!keyword || !env) {
        alert("キーワードと開発環境を選択してください");
        return;
    }

    // 複数形変換を使用
    const Table = pluralize(keyword);
    const Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
    const Controller = Model + "Controller";
    const DB = keyword.toLowerCase() + "_db";
    const Repo = keyword.toLowerCase() + "-app";

    // DBユーザー・パスワード設定
    let dbUser = "root";
    let dbPass = "";
    if (env === "mamp") dbPass = "root";

    // Laravelバージョン指定
    let projectCommand = `composer create-project laravel/laravel ${Repo}`;
    if (laravelVersion) {
        projectCommand = `composer create-project "laravel/laravel=${laravelVersion}" ${Repo}`;
    }

    // 結果テーブル
    const tableHTML = `
        <table class="table table-bordered table-striped mt-3">
          <thead class="table-light">
            <tr><th>項目</th><th>生成結果</th></tr>
          </thead>
          <tbody>
            <tr><td>プロジェクト名</td><td>${Repo}</td></tr>
            <tr><td>GitHubリポジトリ名</td><td>${Repo}</td></tr>
            <tr><td>DB名</td><td>${DB}</td></tr>
            <tr><td>モデル名</td><td>${Model}</td></tr>
            <tr><td>テーブル名</td><td>${Table}</td></tr>
            <tr><td>コントローラ名</td><td>${Controller}</td></tr>
            <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
          </tbody>
        </table>`;
    document.getElementById("result-table").innerHTML = tableHTML;

    // ステップ表示
    document.getElementById("project-create").innerText = projectCommand;
    document.getElementById("cd-project").innerText = `cd ${Repo}`;
    document.getElementById("db-project").innerText = `${Repo}`;
    document.getElementById("remote-add").innerText = `git remote add origin https://github.com/ユーザー名/${Repo}.git`;
    document.getElementById("first-commit").innerText = `git add .\ngit commit -m "first commit"\ngit branch -M main\ngit push -u origin main`;
    document.getElementById("env-config").innerText = `DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=${DB}\nDB_USERNAME=${dbUser}\nDB_PASSWORD=${dbPass}`;
    document.getElementById("create-db").innerText = `CREATE DATABASE ${DB}`;
    document.getElementById("migration").innerText = `php artisan make:migration create_${Table}_table --create=${Table}`;
    document.getElementById("model").innerText = `php artisan make:model ${Model}`;
    document.getElementById("controller").innerText = `php artisan make:controller ${Controller}`;
    document.getElementById("route").innerText = `Route::get('/${Table}', [App\\Http\\Controllers\\${Controller}::class, 'index'])->name('${Table}.index');`;
    document.getElementById("view").innerText = `return view('${Table}/index', compact('${Table}'));`;
    document.getElementById("last-commit").innerText = `git add .\ngit commit -m "初期設定完了"\ngit push origin main`;

    document.getElementById("steps").style.display = "block";
}

function copyCode(button) {
    const code = button.closest(".code-container").querySelector("code").innerText;
    navigator.clipboard.writeText(code);
    button.innerText = "✅ コピー済み";
    setTimeout(() => (button.innerText = "📋 コピー"), 1500);
}
