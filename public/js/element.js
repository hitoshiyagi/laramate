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

    // フォームページ（create/edit）のときだけ実行
    if (projectNameInput && repoNameInput) {
        repoNameInput.readOnly = true;
        if (projectNameInput.value)
            repoNameInput.value = projectNameInput.value;
    }

    // プレビュー表示
    if (previewBtn) {
        previewBtn.addEventListener("click", () => {
            const keyword = document.getElementById("keyword").value.trim();
            const env = document.getElementById("env-select").value;
            const laravelVersion =
                document.getElementById("laravel-version").value;
            const projectName = projectNameInput
                ? projectNameInput.value.trim()
                : "";

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

            const resultTable = document.getElementById("result-table");
            if (resultTable) resultTable.innerHTML = tableHTML;

            const generationResult =
                document.getElementById("generation-result");
            if (generationResult) generationResult.style.display = "block";

            if (registerBtn) registerBtn.style.display = "inline-block";
            if (clearBtn) clearBtn.style.display = "inline-block";

            const stepsArea = document.getElementById("generation-steps-area");
            if (stepsArea) stepsArea.style.display = "none";
        });
    }

    // DB登録
    if (registerBtn) {
        registerBtn.addEventListener("click", () => {
            const keyword = document.getElementById("keyword").value.trim();
            const env = document.getElementById("env-select").value;
            const laravelVersion =
                document.getElementById("laravel-version").value;
            const projectName = projectNameInput ? projectNameInput.value : "";

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
                .then((res) => {
                    if (!res.ok)
                        throw new Error(`サーバーエラー: ${res.status}`);
                    return res.json();
                })
                .then((data) => {
                    const messageDiv =
                        document.getElementById("generation-message");
                    if (data.success) {
                        if (messageDiv) {
                            messageDiv.textContent = "要素名を登録しました。";
                            messageDiv.style.display = "block";
                        }

                        const container =
                            document.getElementById("generation-steps");
                        if (container) container.innerHTML = "";

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

                        const stepsArea = document.getElementById(
                            "generation-steps-area"
                        );
                        if (stepsArea) stepsArea.style.display = "block";
                    } else {
                        alert("登録に失敗しました: " + data.message);
                    }
                })
                .catch((err) => alert(err.message));
        });
    }

    // クリアボタン
    if (clearBtn) {
        clearBtn.addEventListener("click", () => {
            document.getElementById("keyword").value = "";
            document.getElementById("env-select").value = "";
            document.getElementById("laravel-version").value = "";
            if (projectNameInput) projectNameInput.value = "";
            if (repoNameInput) repoNameInput.value = "";
            const generationResult =
                document.getElementById("generation-result");
            if (generationResult) generationResult.style.display = "none";
            if (registerBtn) registerBtn.style.display = "none";
            if (clearBtn) clearBtn.style.display = "none";
            const resultTable = document.getElementById("result-table");
            if (resultTable) resultTable.innerHTML = "";
            const stepsContainer = document.getElementById("generation-steps");
            if (stepsContainer) stepsContainer.innerHTML = "";
            const stepsArea = document.getElementById("generation-steps-area");
            if (stepsArea) stepsArea.style.display = "none";
            const messageDiv = document.getElementById("generation-message");
            if (messageDiv) messageDiv.style.display = "none";
        });
    }

    // 子要素の削除
    document.querySelectorAll(".delete-element").forEach((btn) => {
        btn.addEventListener("click", () => {
            const elementId = btn.dataset.id;
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            if (!confirm("本当に削除しますか？")) return;

            fetch(`/elements/${elementId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                },
            })
                .then((res) => {
                    if (!res.ok)
                        throw new Error(`サーバーエラー: ${res.status}`);
                    return res.json();
                })
                .then((data) => {
                    if (data.success) {
                        const elementDiv = document.getElementById(
                            `element-${elementId}`
                        );
                        if (elementDiv) elementDiv.remove();
                    } else {
                        alert(data.message || "削除できませんでした");
                    }
                })
                .catch((err) => alert(err.message));
        });
    });
});
