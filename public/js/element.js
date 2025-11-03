document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const form = document.getElementById("element-form");

    if (!form) {
        console.log("form が見つかりません");
        return;
    }

    console.log("form 要素を検出。submit イベント登録完了予定。");

    // -----------------------------
    // 登録処理
    // -----------------------------
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        if (!confirm("要素群を登録していいですか？")) {
            return;
        }

        const projectName = document.getElementById(
            "element-project-name"
        ).value;
        const keyword = document.getElementById("keyword").value;
        const env = document.getElementById("env-select").value;
        const laravelVersion = document.getElementById("laravel-version").value;

        fetch("/elements/store", {
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
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    console.log("要素登録成功:", data.element);

                    // ✅ 結果表示エリア
                    const resultDiv =
                        document.getElementById("generation-result");
                    const output = document.getElementById("result-output");
                    const stepsList =
                        document.getElementById("generation-steps");

                    resultDiv.style.display = "block";

                    output.textContent =
                        `プロジェクト名：${data.element.project_name}\n` +
                        `リポジトリ名：${data.element.repository}\n` +
                        `要素名（キーワード）：${data.element.keyword}\n` +
                        `開発環境：${data.element.env}\n` +
                        `Laravelバージョン：${data.element.laravel_version}`;

                    // ✅ 手順リストの生成
                    stepsList.innerHTML = "";
                    data.steps.forEach((step) => {
                        const li = document.createElement("li");
                        li.textContent = step;
                        stepsList.appendChild(li);
                    });

                    // ✅ 登録後に一覧を更新
                    loadElements();
                } else {
                    alert("登録に失敗しました: " + data.message);
                }
            })
            .catch((err) => {
                console.error("通信エラー:", err);
                alert("通信エラーが発生しました。");
            });
    });

    // -----------------------------
    // 一覧表示処理
    // -----------------------------
    function loadElements() {
        fetch("/elements")
            .then((res) => res.json())
            .then((data) => {
                if (!data.success) return;

                const tableDiv = document.getElementById("result-table");
                tableDiv.innerHTML = ""; // 既存内容をクリア

                const table = document.createElement("table");
                table.classList.add("table", "table-bordered");

                const thead = document.createElement("thead");
                thead.innerHTML = `
                    <tr>
                        <th>プロジェクト名</th>
                        <th>GitHubリポジトリ名</th>
                        <th>キーワード</th>
                        <th>開発環境</th>
                        <th>Laravelバージョン</th>
                        <th>作成日</th>
                    </tr>
                `;
                table.appendChild(thead);

                const tbody = document.createElement("tbody");
                data.elements.forEach((el) => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${el.project_name}</td>
                        <td>${el.repository}</td>
                        <td>${el.keyword}</td>
                        <td>${el.env}</td>
                        <td>${el.laravel_version}</td>
                        <td>${el.created_at}</td>
                    `;
                    tbody.appendChild(tr);
                });

                table.appendChild(tbody);
                tableDiv.appendChild(table);
            });
    }

    // -----------------------------
    // 初期読み込みで一覧表示
    // -----------------------------
    loadElements();
});
