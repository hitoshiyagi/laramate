// element.js 完全版
document.addEventListener("DOMContentLoaded", () => {
    // 英単語複数形変換（簡易）
    function pluralize(word) {
        word = word.toLowerCase();
        if (word.endsWith("y")) return word.slice(0, -1) + "ies";
        if (
            ["s", "x", "z"].includes(word.slice(-1)) ||
            word.endsWith("ch") ||
            word.endsWith("sh")
        )
            return word + "es";
        return word + "s";
    }

    // 生成ボタン
    const previewBtn = document.getElementById("preview-elements");
    const registerBtn = document.getElementById("register-elements");
    const clearBtn = document.getElementById("clear-elements");

    previewBtn.addEventListener("click", () => {
        const keyword = document.getElementById("keyword").value.trim();
        const env = document.getElementById("env-select").value;
        const laravelVersion = document.getElementById("laravel-version").value;
        const projectName = document.getElementById(
            "element-project-name"
        ).value;

        if (!keyword || !env || !laravelVersion) {
            alert("キーワード・環境・Laravelバージョンを選択してください");
            return;
        }

        const Table = pluralize(keyword);
        const Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
        const Controller = Model + "Controller";
        const DB = keyword.toLowerCase() + "_db";
        const Repo = keyword.toLowerCase() + "-app";

        // プレビュー表示
        const tableHTML = `
            <table class="table table-bordered table-striped mt-3">
                <thead><tr><th>項目</th><th>生成結果</th></tr></thead>
                <tbody>
                    <tr><td>プロジェクト名</td><td>${projectName}</td></tr>
                    <tr><td>GitHubリポジトリ名</td><td>${Repo}</td></tr>
                    <tr><td>DB名</td><td>${DB}</td></tr>
                    <tr><td>モデル名</td><td>${Model}</td></tr>
                    <tr><td>テーブル名</td><td>${Table}</td></tr>
                    <tr><td>コントローラ名</td><td>${Controller}</td></tr>
                    <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
                </tbody>
            </table>`;
        document.getElementById("result-table").innerHTML = tableHTML;

        // ボタン表示
        document.getElementById("generation-result").style.display = "block";
        registerBtn.style.display = "inline-block";
        clearBtn.style.display = "inline-block";
        document.getElementById("generation-steps-area").style.display = "none";
    });

    // DB登録
    registerBtn.addEventListener("click", () => {
        const keyword = document.getElementById("keyword").value.trim();
        const env = document.getElementById("env-select").value;
        const laravelVersion = document.getElementById("laravel-version").value;
        const projectName = document.getElementById(
            "element-project-name"
        ).value;

        fetch("/elements/store", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                project_name: projectName,
                keyword,
                env,
                laravel_version: laravelVersion,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
        const messageDiv = document.getElementById("generation-message");
        if (data.success) {
            // ページ内に表示
            messageDiv.textContent = "要素名を登録しました。";
            messageDiv.style.display = "block";
            // 手順表示
            const steps = data.steps.map((s) => `<li>${s}</li>`).join("");
            document.getElementById("generation-steps").innerHTML = steps;
            document.getElementById("generation-steps-area").style.display =
                "block";
        } else {
            alert("登録に失敗しました: " + data.message);
        }
            })
            .catch(() => alert("通信エラーが発生しました"));
    });

    // クリアボタン
    clearBtn.addEventListener("click", () => {
        document.getElementById("keyword").value = "";
        document.getElementById("env-select").value = "";
        document.getElementById("laravel-version").value = "";
        document.getElementById("generation-result").style.display = "none";
        registerBtn.style.display = "none";
        clearBtn.style.display = "none";
        document.getElementById("result-table").innerHTML = "";
        document.getElementById("generation-steps").innerHTML = "";
        document.getElementById("generation-steps-area").style.display = "none";
    });
});
