// 英単語を複数形に変換する関数（簡易版）
function pluralize(word) {
    word = word.toLowerCase();
    if (word.endsWith("y")) {
        return word.slice(0, -1) + "ies"; // city → cities
    } else if (
        word.endsWith("s") ||
        word.endsWith("x") ||
        word.endsWith("z") ||
        word.endsWith("ch") ||
        word.endsWith("sh")
    ) {
        return word + "es"; // bus → buses, box → boxes
    } else {
        return word + "s"; // cat → cats
    }
}

// コードをコピーする関数
function copyCode(button) {
    const code = button
        .closest(".code-container")
        .querySelector("code").innerText;
    navigator.clipboard.writeText(code);
    button.innerText = "✅ コピー済み";
    setTimeout(() => (button.innerText = "📋 コピー"), 1500);
}
