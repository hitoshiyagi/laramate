document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ===== Utility Functions =====
    const allowAlphanumericOnly = (input) =>
        (input.target.value = input.target.value.replace(/[^a-zA-Z0-9]/g, ""));

    const alertMsg = (msg) => alert(msg);

    const scrollToEl = (el) => el?.scrollIntoView({ behavior: "smooth" });

    const pluralize = (word) => {
        word = word.toLowerCase();
        if (word.endsWith("y")) return word.slice(0, -1) + "ies";
        if (
            ["s", "x", "z"].includes(word.slice(-1)) ||
            word.endsWith("ch") ||
            word.endsWith("sh")
        )
            return word + "es";
        return word + "s";
    };

    // ===== DOM Elements =====
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

    // ===== 半角英数字制限 =====
    document.querySelectorAll('input[type="text"]').forEach((input) => {
        input.setAttribute("inputmode", "latin");
        input.setAttribute("lang", "en");
        input.addEventListener("focus", () => input.setAttribute("lang", "en"));
        input.addEventListener("input", allowAlphanumericOnly);
    });

    // ===== プロジェクト作成 =====
    createProjectBtn?.addEventListener("click", async () => {
        const name = projectNameInput.value.trim();
        if (!name) return alertMsg("プロジェクト名を入力してください");

        try {
            const checkRes = await fetch(
                `/projects/check-name?name=${encodeURIComponent(name)}`
            );
            const checkData = await checkRes.json();
            if (checkData.exists)
                return alertMsg("このプロジェクト名は既に使用されています");

            const res = await fetch("/projects", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: new URLSearchParams({ name }),
            });

            const data = await res.json();
            if (data.success) {
                elementProjectName.value = data.project.name;
                elementProjectRepo.value = data.project.repo;
                elementProjectDb.value = data.project.database_name;

                projectCard.style.display = "none";
                elementCard.style.display = "block";
            } else {
                alertMsg(data.message || "作成に失敗しました。");
            }
        } catch (err) {
            console.error(err);
            alertMsg("通信エラーが発生しました");
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
            return alertMsg(
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
        scrollToEl(resultTable);
    });

    // ===== 要素登録 =====
    registerBtn?.addEventListener("click", async () => {
        const projectName = elementProjectName.value.trim();
        const keyword = keywordInput.value.trim();
        const env = envSelect.value;
        const laravelVersion = laravelVersionSelect.value;

        if (!projectName || !keyword || !env || !laravelVersion)
            return alertMsg("すべての項目を入力してください。");

        try {
            const checkParams = new URLSearchParams({
                name: keyword,
                project_name: projectName,
            });
            const checkRes = await fetch(
                `/elements/check?${checkParams.toString()}`
            );
            const checkData = await checkRes.json();
            if (checkData.exists)
                return alertMsg(
                    `要素名 "${keyword}" はこのプロジェクト内で既に使用されています`
                );

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
                scrollToEl(stepsContainer);
            } else {
                alertMsg(data.message || "登録に失敗しました。");
            }
        } catch (err) {
            console.error(err);
            alertMsg("通信エラーが発生しました");
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
        [
            projectNameInput,
            elementProjectName,
            elementProjectRepo,
            elementProjectDb,
            keywordInput,
        ].forEach((el) => (el.value = ""));
        [envSelect, laravelVersionSelect].forEach((el) => (el.value = ""));
        resultTable.innerHTML = "";
        [generationResult, stepsArea, messageDiv].forEach(
            (el) => (el.style.display = "none")
        );
    });
});
