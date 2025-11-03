.then((data) => {
    if (data.success) {
        console.log("要素登録成功:", data.element);

        // ✅ 結果表示エリアを表示
        const resultDiv = document.getElementById("generation-result");
        const output = document.getElementById("result-output");
        const stepsList = document.getElementById("generation-steps");

        resultDiv.style.display = "block";

        // ✅ 登録内容を人間向けに表示
        output.textContent = 
            `プロジェクト名：${data.element.project_id}\n` +
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
    } else {
        alert("登録に失敗しました: " + data.message);
    }
})
