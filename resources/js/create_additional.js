// public/js/create_additional.js

document.addEventListener("DOMContentLoaded", () => {
    // キーワードは半角英数字のみ
    const keywordInput = document.getElementById("keyword");

    keywordInput.addEventListener("input", () => {
        keywordInput.value = keywordInput.value.replace(/[^a-zA-Z0-9]/g, "");
    });
});

document.addEventListener("DOMContentLoaded", () => {
    // ---------------------------------------
    // 自動生成：スネークケースへ変換
    // ---------------------------------------
    function toSnakeCase(text) {
        return text
            .replace(/\s+/g, "_")
            .replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`)
            .replace(/-+/g, "_")
            .replace(/__+/g, "_")
            .replace(/^_+|_+$/g, "")
            .toLowerCase();
    }

    // ---------------------------------------
    // 行追加
    // ---------------------------------------
    const addRowBtn = document.getElementById("addRowBtn");
    const rowsContainer = document.getElementById("rowsContainer");

    addRowBtn.addEventListener("click", () => {
        const newRow = document.querySelector(".row-template").cloneNode(true);
        newRow.classList.remove("row-template");
        newRow.classList.add("row-item");
        newRow.style.display = "flex"; // テンプレは非表示なので表示

        rowsContainer.appendChild(newRow);
        reindexRows();
    });

    // ---------------------------------------
    // 行削除（イベント委譲）
    // ---------------------------------------
    rowsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("deleteRowBtn")) {
            const row = e.target.closest(".row-item");
            row.remove();
            reindexRows();
        }
    });

    // ---------------------------------------
    // ラベル変更 → 自動生成更新
    // ---------------------------------------
    rowsContainer.addEventListener("input", (e) => {
        if (e.target.classList.contains("labelInput")) {
            const row = e.target.closest(".row-item");
            const label = e.target.value;
            row.querySelector(".nameInput").value = toSnakeCase(label);
        }
    });

    // ---------------------------------------
    // 行番号振り直し
    // name="fields[0][label]" → fields[1][label]…
    // ---------------------------------------
    function reindexRows() {
        const allRows = document.querySelectorAll(".row-item");

        allRows.forEach((row, index) => {
            row.querySelectorAll("input, select").forEach((input) => {
                const name = input.getAttribute("name");
                if (name) {
                    const newName = name.replace(
                        /fields\[\d+\]/,
                        `fields[${index}]`
                    );
                    input.setAttribute("name", newName);
                }
            });
        });
    }
});
