document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // -------------------------------
    // ユーティリティ関数
    // -------------------------------
    const enforceAlphanumeric = (input) => {
        input.value = input.value.replace(/[^a-zA-Z0-9]/g, "");
    };

    const showError = (selector, message) => {
        const el = document.getElementById(selector);
        if (el) el.textContent = message;
    };

    const fetchJSON = async (url, options = {}) => {
        try {
            const res = await fetch(url, options);
            return await res.json();
        } catch (err) {
            console.error(err);
            throw new Error("通信エラーが発生しました。");
        }
    };

    const confirmAndDelete = async (url, successCallback) => {
        if (!confirm("本当に削除しますか？")) return;
        try {
            const data = await fetchJSON(url, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            });
            if (data.success && successCallback) successCallback(data);
            else alert(data.message || "削除に失敗しました");
        } catch (err) {
            alert(err.message);
        }
    };

    // -------------------------------
    // 半角英数字制限
    // -------------------------------
    document.querySelectorAll('input[type="text"]').forEach((input) => {
        input.setAttribute("inputmode", "latin");
        input.setAttribute("lang", "en");
        input.addEventListener("focus", () => input.setAttribute("lang", "en"));
        input.addEventListener("input", () => enforceAlphanumeric(input));
    });

    // -------------------------------
    // プロジェクト作成
    // -------------------------------
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
                const checkData = await fetchJSON(
                    `/projects/check-name?name=${encodeURIComponent(name)}`
                );
                if (checkData.exists)
                    return showError(
                        "project-error",
                        "このプロジェクト名はすでに存在します。"
                    );

                const data = await fetchJSON("/projects", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        name,
                        repo: name,
                        database_name: name + "_db",
                    }),
                });

                if (data.success) {
                    ["name", "repo", "db"].forEach((key) => {
                        const el = document.getElementById(
                            `element-project-${key}`
                        );
                        if (el)
                            el.value =
                                data.project[
                                    key === "db" ? "database_name" : key
                                ];
                    });
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
                showError("project-error", err.message);
            }
        });
    }

    // -------------------------------
    // プロジェクト削除
    // -------------------------------
    document.querySelectorAll(".delete-project").forEach((btn) => {
        btn.addEventListener("click", () =>
            confirmAndDelete(
                `/projects/${btn.dataset.id}`,
                () => (location.href = "/projects")
            )
        );
    });

    // -------------------------------
    // 子要素削除
    // -------------------------------
    document.querySelectorAll(".delete-element-icon").forEach((btn) => {
        btn.addEventListener("click", () =>
            confirmAndDelete(`/elements/${btn.dataset.id}`, () => {
                const elCard = document.getElementById(
                    `element-${btn.dataset.id}`
                );
                if (elCard) elCard.remove();
            })
        );
    });
});
