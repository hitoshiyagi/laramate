document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // 半角英数字制限
    const enforceAlphanumeric = (input) => {
        input.value = input.value.replace(/[^a-zA-Z0-9]/g, "");
    };
    document.querySelectorAll('input[type="text"]').forEach((input) => {
        input.setAttribute("inputmode", "latin");
        input.setAttribute("lang", "en");
        input.addEventListener("focus", () => input.setAttribute("lang", "en"));
        input.addEventListener("input", () => enforceAlphanumeric(input));
    });

    const showError = (selector, message) => {
        const el = document.getElementById(selector);
        if (el) el.textContent = message;
    };

    // プロジェクト作成
    const createBtn = document.getElementById("create-project-btn");
    if (createBtn) {
        createBtn.addEventListener("click", async () => {
            const nameInput = document.getElementById("name");
            const name = nameInput.value.trim();
            showError("project-error", "");
            if (!name)
                return showError(
                    "project-error",
                    "プロジェクト名を入力してください。"
                );

            try {
                const checkRes = await fetch(
                    `/projects/check-name?name=${encodeURIComponent(name)}`
                );
                const checkData = await checkRes.json();
                if (checkData.exists)
                    return showError(
                        "project-error",
                        "このプロジェクト名はすでに存在します。"
                    );

                const res = await fetch("/projects", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        name: name,
                        repo: name,
                        database_name: name + "_db",
                    }),
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById("element-project-name").value =
                        data.project.name;
                    document.getElementById("element-project-repo").value =
                        data.project.repo;
                    document.getElementById("element-project-db").value =
                        data.project.database_name;

                    document.getElementById("project-card").style.display =
                        "none";
                    document.getElementById("element-card").style.display =
                        "block";
                } else if (data.errors?.name) {
                    showError("project-error", data.errors.name[0]);
                } else {
                    showError(
                        "project-error",
                        "プロジェクトの作成に失敗しました。"
                    );
                }
            } catch (err) {
                console.error(err);
                showError("project-error", "通信エラーが発生しました。");
            }
        });
    }

    // プロジェクト削除
    const deleteProject = async (btn) => {
        if (!confirm("本当にプロジェクトを削除しますか？")) return;
        try {
            const res = await fetch(`/projects/${btn.dataset.id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                location.href = "/projects";
            } else alert(data.message || "削除に失敗しました");
        } catch (err) {
            console.error(err);
            alert("通信エラーが発生しました");
        }
    };
    document
        .querySelectorAll(".delete-project")
        .forEach((btn) =>
            btn.addEventListener("click", () => deleteProject(btn))
        );

    // 子要素削除
    const deleteElement = async (btn) => {
        if (!confirm("本当にこの要素を削除しますか？")) return;
        try {
            const res = await fetch(`/elements/${btn.dataset.id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            });
            const data = await res.json();
            if (data.success) {
                const elCard = document.getElementById(
                    `element-${btn.dataset.id}`
                );
                if (elCard) elCard.remove();
            } else alert(data.message || "削除に失敗しました");
        } catch (err) {
            console.error(err);
            alert("通信エラーが発生しました");
        }
    };
    document
        .querySelectorAll(".delete-element-icon")
        .forEach((btn) =>
            btn.addEventListener("click", () => deleteElement(btn))
        );
});
