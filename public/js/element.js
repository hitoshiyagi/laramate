document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ======== DOM取得 ========
    const projectCard = document.getElementById("project-card");
    const elementCard = document.getElementById("element-card");
    const createProjectBtn = document.getElementById("create-project-btn");
    const projectNameInput = document.getElementById("project-name");
    const elementProjectName = document.getElementById("element-project-name");

    const previewBtn = document.getElementById("preview-elements");
    const registerBtn = document.getElementById("register-elements");
    const clearBtn = document.getElementById("clear-elements");

    const keywordInput = document.getElementById("keyword");
    const envSelect = document.getElementById("env-select");
    const laravelVersionSelect = document.getElementById("laravel-version");

    const resultTable = document.getElementById("result-table");
    const generationResult = document.getElementById("generation-result");
    const stepsArea = document.getElementById("generation-steps-area");
    const stepsContainer = document.getElementById("generation-steps");
    const messageDiv = document.getElementById("generation-message");

    let Table, Model, Controller, DB;

    // ======== 半角英数字制限 ========
    function allowAlphanumericOnly(event) {
        event.target.value = event.target.value.replace(/[^a-zA-Z0-9]/g, "");
    }
    document.querySelectorAll('input[type="text"]').forEach((input) => {
        input.addEventListener("input", allowAlphanumericOnly);
    });

    // ======== 単語変換 ========
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

    // ======== プロジェクト作成 ========
    createProjectBtn?.addEventListener("click", async () => {
        const name = projectNameInput.value.trim();
        if (!name) {
            alert("プロジェクト名を入力してください");
            return;
        }

        try {
            const res = await fetch("/projects", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ name }),
            });

            if (!res.ok) throw new Error(`サーバーエラー: ${res.status}`);
            const data = await res.json();

            if (data.success) {
                elementProjectName.value = data.project.name;
                projectCard.style.display = "none";
                elementCard.style.display = "block";
                elementProjectName.value = data.project.name;
                // リポジトリ名も連動
                const repoInput = document.getElementById(
                    "element-project-repo"
                );
                if (repoInput) repoInput.value = data.project.name;
            } else {
                document.getElementById("project-error").textContent =
                    data.errors?.name?.[0] ||
                    data.message ||
                    "作成に失敗しました";
            }
        } catch (err) {
            console.error(err);
            alert("通信エラーが発生しました");
        }
    });

    // ======== プレビュー生成 ========
    previewBtn?.addEventListener("click", () => {
        const keyword = keywordInput.value.trim();
        const env = envSelect.value;
        const laravelVersion = laravelVersionSelect.value;
        const projectName = elementProjectName.value.trim();

        if (!keyword || !env || !laravelVersion) {
            alert("キーワード・環境・Laravelバージョンを選択してください");
            return;
        }

        Table = pluralize(keyword);
        Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
        Controller = Model + "Controller";
        DB = keyword.toLowerCase() + "_db";

        resultTable.innerHTML = `
            <table class="table table-bordered table-striped mt-3">
                <thead><tr><th>項目</th><th>生成結果</th></tr></thead>
                <tbody>
                    <tr><td>プロジェクト名</td><td>${projectName}</td></tr>
                    <tr><td>GitHubリポジトリ名</td><td>${projectName}</td></tr>
                    <tr><td>データベース名</td><td>${DB}</td></tr>
                    <tr><td>モデル名</td><td>${Model}</td></tr>
                    <tr><td>テーブル名</td><td>${Table}</td></tr>
                    <tr><td>コントローラ名</td><td>${Controller}</td></tr>
                    <tr><td>ビュー</td><td>${Table}/index.blade.php</td></tr>
                </tbody>
            </table>
        `;
        generationResult.style.display = "block";
        registerBtn.style.display = "inline-block";
        clearBtn.style.display = "inline-block";
        stepsArea.style.display = "none";
        messageDiv.style.display = "none";

        // スクロールで見えるように
        resultTable.scrollIntoView({ behavior: "smooth" });
    });

    // ======== 要素登録 ========
    registerBtn?.addEventListener("click", async () => {
        const projectName = elementProjectName.value.trim();
        const keyword = keywordInput.value.trim();
        const env = envSelect.value;
        const laravelVersion = laravelVersionSelect.value;

        if (!projectName || !keyword || !env || !laravelVersion) {
            alert("すべての項目を入力してください。");
            return;
        }

        try {
            const res = await fetch("/elements", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
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
            });

            if (!res.ok) throw new Error(`サーバーエラー: ${res.status}`);
            const data = await res.json();

            if (data.success) {
                messageDiv.textContent = "要素を登録しました。";
                messageDiv.style.display = "block";

                // 手順表示関数
                displaySteps(data.steps);
                stepsContainer.scrollIntoView({ behavior: "smooth" });
            } else {
                alert(data.message || "登録に失敗しました。");
            }
        } catch (err) {
            console.error(err);
            alert("通信エラーが発生しました");
        }
    });

    // ==========================
    // 手順表示＆コピー機能
    // ==========================
    function displaySteps(steps) {
        const stepsContainer = document.getElementById("generation-steps");
        const stepsArea = document.getElementById("generation-steps-area");
        stepsContainer.innerHTML = "";

        steps.forEach((step, index) => {
            const div = document.createElement("div");
            div.classList.add("step-card");

            div.innerHTML = `
            <h5 class="fw-bold">${step.title}</h5>
            <p>${step.description}</p>
            ${
                step.command
                    ? `<div class="code-header">
                            <button class="copy-btn"><i class="far fa-copy"></i> コードをコピーする</button>
                       </div>
                       <pre class="code-block">${step.command}</pre>`
                    : ""
            }
        `;

            stepsContainer.appendChild(div);
        });

        stepsArea.style.display = "block";

        // コピー機能（アイコン保持）
        const copyButtons = stepsContainer.querySelectorAll(".copy-btn");
        copyButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                const code = btn.parentElement.nextElementSibling.innerText;
                navigator.clipboard.writeText(code);

                // 文字を変更してもアイコンは残す
                btn.innerHTML =
                    '<i class="far fa-copy"></i> コードをコピーしました';
                setTimeout(() => {
                    btn.innerHTML =
                        '<i class="far fa-copy"></i> コードをコピーする';
                }, 1500);
            });
        });
    }

    // ======== クリア ========
    clearBtn?.addEventListener("click", () => {
        projectNameInput.value = "";
        elementProjectName.value = "";
        keywordInput.value = "";
        envSelect.value = "";
        laravelVersionSelect.value = "";

        resultTable.innerHTML = "";
        generationResult.style.display = "none";
        stepsArea.style.display = "none";
        messageDiv.style.display = "none";
    });
});
