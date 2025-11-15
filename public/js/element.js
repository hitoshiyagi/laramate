document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ===== 半角英数字制限 =====
    function allowAlphanumericOnly(event) {
        event.target.value = event.target.value.replace(/[^a-zA-Z0-9]/g, "");
    }
    document.querySelectorAll('input[type="text"]').forEach((input) => {
        input.setAttribute("inputmode", "latin");
        input.setAttribute("lang", "en");
        input.addEventListener("focus", () => input.setAttribute("lang", "en"));
        input.addEventListener("input", allowAlphanumericOnly);
    });

    const projectCard = document.getElementById("project-card");
    const elementCard = document.getElementById("element-card");
    const createProjectBtn = document.getElementById("create-project-btn");
    const projectNameInput = document.getElementById("name");
    const elementProjectName = document.getElementById("element-project-name");
    const elementProjectRepo = document.getElementById("element-project-repo");
    const elementProjectDb = document.getElementById("element-project-db");

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

    // ===== プロジェクト作成・要素画面切替 =====
    createProjectBtn?.addEventListener("click", async () => {
        const name = projectNameInput.value.trim();
        if (!name) return alert("プロジェクト名を入力してください");

        try {
            // プロジェクト重複チェック (GET)
            const params = new URLSearchParams({ name });
            const checkRes = await fetch(
                `/projects/check-name?${params.toString()}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                    },
                }
            );

            const checkData = await checkRes.json();
            if (checkData.exists)
                return alert("このプロジェクト名は既に使用されています");

            // プロジェクト作成 (POST)
            const res = await fetch("/projects", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: new URLSearchParams({ name }),
            });

            // プロジェクト作成処理は成功時にプロジェクトオブジェクト全体を返していると想定
            const data = await res.json();
            if (data.success) {
                // ここでプロジェクトIDが必要になる可能性を考慮し、
                // project-cardに hidden input でIDを持たせるなどの設計変更が必要になる場合がありますが、
                // 今回は name だけを渡し、サーバー側で project_id に変換すると想定します。
                elementProjectName.value = data.project.name;
                elementProjectRepo.value = data.project.repo;
                elementProjectDb.value = data.project.database_name;
                projectCard.style.display = "none";
                elementCard.style.display = "block";
            } else {
                alert(data.message || "作成に失敗しました。");
            }
        } catch (err) {
            console.error(err);
            alert("通信エラーが発生しました");
        }
    });

    // ===== プレビュー生成 =====
    previewBtn?.addEventListener("click", () => {
        const keyword = keywordInput.value.trim();
        const env = envSelect.value;
        const laravelVersion = laravelVersionSelect.value;
        const projectName = elementProjectName.value.trim();
        const dbName = elementProjectDb.value.trim();
        if (!keyword || !env || !laravelVersion)
            return alert(
                "キーワード・環境・Laravelバージョンを選択してください"
            );

        Table = pluralize(keyword);
        Model = keyword.charAt(0).toUpperCase() + keyword.slice(1);
        Controller = Model + "Controller";
        DB = dbName;

        resultTable.innerHTML = `
            <table class="table table-bordered table-striped mt-3">
                <thead><tr><th>項目</th><th>生成結果</th></tr></thead>
                <tbody>
                    <tr><td>プロジェクト名</td><td>${projectName}</td></tr>
                    <tr><td>GitHubリポジトリ名</td><td>${elementProjectRepo.value}</td></tr>
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
        resultTable.scrollIntoView({ behavior: "smooth" });
    });

    // ===== 要素登録 =====
    registerBtn?.addEventListener("click", async () => {
        const projectName = elementProjectName.value.trim();
        const keyword = keywordInput.value.trim();
        const env = envSelect.value;
        const laravelVersion = laravelVersionSelect.value;
        if (!projectName || !keyword || !env || !laravelVersion)
            return alert("すべての項目を入力してください。");

        try {
            // ★★★ 修正箇所：子要素の重複チェックを追加 ★★★
            const checkParams = new URLSearchParams({
                name: keyword,
                // プロジェクト名からIDを特定するために、ここではプロジェクト名を渡します。
                // サーバー側 (ElementController@check) でIDに変換する前提です。
                project_name: projectName,
            });
            const checkRes = await fetch(
                `/elements/check?${checkParams.toString()}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                    },
                }
            );

            const checkData = await checkRes.json();
            if (checkData.exists) {
                return alert(
                    `要素名 "${keyword}" はこのプロジェクト内で既に使用されています`
                );
            }
            // ★★★ 修正箇所終わり ★★★

            // 本登録処理 (POST)
            const res = await fetch("/elements", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: new URLSearchParams({
                    project_name: projectName,
                    keyword,
                    env,
                    laravel_version: laravelVersion,
                    table_name: Table,
                    model_name: Model,
                    controller_name: Controller,
                    database_name: DB,
                }),
            });
            const data = await res.json();
            if (data.success) {
                messageDiv.textContent = "要素を登録しました。";
                messageDiv.style.display = "block";
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

    function displaySteps(steps) {
        stepsContainer.innerHTML = "";
        steps.forEach((step) => {
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

        stepsContainer.querySelectorAll(".copy-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const code = btn.parentElement.nextElementSibling.innerText;
                navigator.clipboard.writeText(code);
                btn.innerHTML =
                    '<i class="far fa-copy"></i> コードをコピーしました';
                setTimeout(
                    () =>
                        (btn.innerHTML =
                            '<i class="far fa-copy"></i> コードをコピーする'),
                    1500
                );
            });
        });
    }

    // ===== クリア =====
    clearBtn?.addEventListener("click", () => {
        projectNameInput.value = "";
        elementProjectName.value = "";
        elementProjectRepo.value = "";
        elementProjectDb.value = "";
        keywordInput.value = "";
        envSelect.value = "";
        laravelVersionSelect.value = "";
        resultTable.innerHTML = "";
        generationResult.style.display = "none";
        stepsArea.style.display = "none";
        messageDiv.style.display = "none";
    });
});
