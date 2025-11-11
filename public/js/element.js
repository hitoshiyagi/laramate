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

    const previewBtn = document.getElementById("preview-elements");
    const registerBtn = document.getElementById("register-elements");
    const clearBtn = document.getElementById("clear-elements");

    // コードコピー関数
    window.copyCode = function (button) {
        const code = button
            .closest(".code-container")
            .querySelector("code").innerText;
        navigator.clipboard.writeText(code);
        button.textContent = "✅ コピー済み";
        setTimeout(() => (button.textContent = "📋 コピー"), 1500);
    };

    // preview で作った値を register でも使うために上位スコープに置く
    let Table, Model, Controller, DB;

    const projectNameInput = document.getElementById("element-project-name");
    const repoNameInput = document.getElementById("element-project-repo");

    // リポジトリ名はプロジェクト名と同じにしてリードオンリーで表示
    repoNameInput.readOnly = true;
    if (projectNameInput.value) {
        repoNameInput.value = projectNameInput.value;
    }

    // プレビュー表示
    previewBtn.addEventListener("click", () => {
        const keyword = document.getElementById("keyword").value.trim();
        const env = document.getElementById("env-select").value;
        const laravelVersion = document.getElementById("laravel-version").value;
        const projectName = projectNameInput.value.trim();

        if (!keyword || !env || !laravelVersion) {
            alert("キーワード・環境・Laravelバージョンを選択してください");
            return;
        }

        Table = pluralize(keyword);
        Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
        Controller = Model + "Controller";
        DB = keyword.toLowerCase() + "_db";

        const tableHTML = `
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr><th>項目</th><th>生成結果</th></tr>
                </thead>
                <tbody>
                    <tr><td>プロジェクト名</td><td>${projectName}</td></tr>
                    <tr><td>GitHubリポジトリ名</td><td>${projectName}</td></tr>
                    <tr><td>データベース名</td><td>${DB}</td></tr>
                    <tr><td>モデル名</td><td>${Model}</td></tr>
                    <tr><td>テーブル名</td><td>${Table}</td></tr>
                    <tr><td>コントローラ名</td><td>${Controller}</td></tr>
                    <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
                </tbody>
            </table>`;

        document.getElementById("result-table").innerHTML = tableHTML;
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
        const projectName = projectNameInput.value;

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
                table_name: Table,
                model_name: Model,
                controller_name: Controller,
                db_name: DB,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                const messageDiv =
                    document.getElementById("generation-message");
                if (data.success) {
                    messageDiv.textContent = "要素名を登録しました。";
                    messageDiv.style.display = "block";

                    const container =
                        document.getElementById("generation-steps");
                    container.innerHTML = "";

                    data.steps.forEach((step, index) => {
                        const div = document.createElement("div");
                        div.classList.add(
                            "step-card",
                            "p-3",
                            "border",
                            "rounded",
                            "bg-light"
                        );
                        div.innerHTML = `
                        <h5 class="fw-bold">${step.title}</h5>
                        <p>${step.description}</p>
                        ${
                            step.command
                                ? `<div class="code-container" style="margin-bottom: 0;">
                            <div class="code-header">
                                💾 コード
                                <button class="copy-btn" onclick="copyCode(this)">📋 コピー</button>
                            </div>
                            <pre class="code-block" style="margin-bottom: 0;"><code>${step.command}</code></pre>
                        </div>`
                                : ""
                        }
                    `;
                        div.style.marginBottom =
                            index === data.steps.length - 1 ? "0" : "20px";
                        container.appendChild(div);
                    });

                    document.getElementById(
                        "generation-steps-area"
                    ).style.display = "block";
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
        projectNameInput.value = "";
        repoNameInput.value = "";
        document.getElementById("generation-result").style.display = "none";
        registerBtn.style.display = "none";
        clearBtn.style.display = "none";
        document.getElementById("result-table").innerHTML = "";
        document.getElementById("generation-steps").innerHTML = "";
        document.getElementById("generation-steps-area").style.display = "none";
        document.getElementById("generation-message").style.display = "none";
    });
});
