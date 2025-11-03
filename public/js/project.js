
document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("create-project-btn");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    if (btn) {
        btn.addEventListener("click", () => {
            const nameInput = document.getElementById("name");
            const name = nameInput.value.trim();

            if (!name) {
                alert("プロジェクト名を入力してください。");
                return;
            }

            fetch("/projects/store", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ name }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // ✅ 要素群生成カードを表示
                        document.getElementById("element-card").style.display =
                            "block";

                        // ✅ プロジェクト名を反映
                        document.getElementById("project-name").textContent =
                            data.project.name;
                        document.getElementById("element-project-name").value =
                            data.project.name;

                        // ✅ プロジェクト作成カードを非表示
                        document.getElementById("project-card").style.display =
                            "none";
                    } else {
                        alert("プロジェクトの作成に失敗しました。");
                    }
                })
                .catch(() => {
                    alert("通信エラーが発生しました。");
                });
        });
    }
});
