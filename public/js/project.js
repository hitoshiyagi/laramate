document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("create-project-btn");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    if (btn) {
        btn.addEventListener("click", () => {
            const nameInput = document.getElementById("name");
            const name = nameInput.value.trim();
            const errorDiv = document.getElementById("project-error");

            // 前回のエラーをクリア
            errorDiv.textContent = "";

            if (!name) {
                errorDiv.textContent = "プロジェクト名を入力してください。";
                return;
            }

            fetch("/projects/store", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    name: name,
                    repo: name,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // 要素名生成カードを表示
                        document.getElementById("element-card").style.display =
                            "block";

                        // プロジェクト名・リポジトリ名を反映
                        document.getElementById("element-project-name").value =
                            data.project.name;
                        document.getElementById("element-project-repo").value =
                            data.project.repo;

                        // プロジェクト作成カードを非表示
                        document.getElementById("project-card").style.display =
                            "none";
                    } else if (data.errors && data.errors.name) {
                        // バリデーションエラーを表示
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
});
