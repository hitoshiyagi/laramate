document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // プロジェクト作成
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

            fetch("/projects", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ name: name, repo: name }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // プロジェクト作成後に要素カード表示
                        const elementCard =
                            document.getElementById("element-card");
                        if (elementCard) elementCard.style.display = "block";

                        const projectNameInput = document.getElementById(
                            "element-project-name"
                        );
                        const repoInput = document.getElementById(
                            "element-project-repo"
                        );
                        if (projectNameInput)
                            projectNameInput.value = data.project.name;
                        if (repoInput) repoInput.value = data.project.repo;

                        const projectCard =
                            document.getElementById("project-card");
                        if (projectCard) projectCard.style.display = "none";
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

    // 親プロジェクト削除（イベント委譲）
    document.addEventListener("click", (e) => {
        const target = e.target.closest(".delete-project");
        if (!target) return;

        const projectId = target.getAttribute("data-id");
        if (!projectId) return;

        if (!confirm("本当にこのプロジェクトと全ての要素を削除しますか？"))
            return;

        fetch(`/projects/${projectId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error(`サーバーエラー: ${res.status}`);
                return res.json();
            })
            .then((data) => {
                if (data.success) {
                    const projectCard = document.getElementById(
                        `project-${projectId}`
                    );
                    if (projectCard) projectCard.remove();
                } else {
                    alert(data.message || "削除に失敗しました");
                }
            })
            .catch((err) => alert(err.message || "通信エラーが発生しました"));
    });
});
