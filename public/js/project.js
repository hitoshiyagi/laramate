document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // ===== プロジェクト作成 =====
    const btn = document.getElementById("create-project-btn");
    if (btn) {
        btn.addEventListener("click", () => {
            const nameInput = document.getElementById("name");
            const name = nameInput.value.trim();
            const errorDiv = document.getElementById("project-error");
            errorDiv.textContent = "";

            if (!name) {
                errorDiv.textContent = "プロジェクト名を入力してください。";
                return;
            }

            // ★ repo と database を name から自動生成
            const repoName = name;
            const dbName = name + "_db";

            fetch("/projects", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    name: name,
                    repo: repoName,
                    database: dbName,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // カード切り替え
                        const elementCard =
                            document.getElementById("element-card");
                        const projectCard =
                            document.getElementById("project-card");
                        if (elementCard) elementCard.style.display = "block";
                        if (projectCard) projectCard.style.display = "none";

                        // ==== Element 側へ値を渡す ====
                        document.getElementById("element-project-name").value =
                            data.project.name;

                        document.getElementById("element-project-repo").value =
                            data.project.repo;

                        document.getElementById("element-project-db").value =
                            data.project.database;
                    } else if (data.errors && data.errors.name) {
                        errorDiv.textContent = data.errors.name[0];
                    } else {
                        errorDiv.textContent =
                            "プロジェクトの作成に失敗しました。";
                    }
                })
                .catch(() => {
                    errorDiv.textContent = "通信エラーが発生しました。";
                });
        });
    }

    // ===== 親プロジェクト削除 =====
document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // 親プロジェクト削除
    document.querySelectorAll(".delete-project").forEach((btn) => {
        btn.addEventListener("click", async () => {
            const projectId = btn.dataset.id;
            if (!confirm("本当にプロジェクトを削除しますか？")) return;

            try {
                const res = await fetch(`/projects/${projectId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                });
                const data = await res.json();

                if (data.success) {
                    alert(data.message);
                    location.href = "/projects"; // 一覧に戻る
                } else {
                    alert(data.message || "削除に失敗しました");
                }
            } catch (err) {
                console.error(err);
                alert("通信エラーが発生しました");
            }
        });
    });

    // 子要素削除
    document.querySelectorAll(".delete-element-icon").forEach((btn) => {
        btn.addEventListener("click", async () => {
            const elementId = btn.dataset.id;
            if (!confirm("本当にこの要素を削除しますか？")) return;

            try {
                const res = await fetch(`/elements/${elementId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                });
                const data = await res.json();

                if (data.success) {
                    alert(data.message);
                    const elCard = document.getElementById(`element-${elementId}`);
                    if (elCard) elCard.remove();
                } else {
                    alert(data.message || "削除に失敗しました");
                }
            } catch (err) {
                console.error(err);
                alert("通信エラーが発生しました");
            }
        });
    });
});
