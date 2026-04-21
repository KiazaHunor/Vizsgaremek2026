let started = false;

function startGame() {
    if (started) return;
    started = true;

    const screen = document.getElementById("startScreen");
    screen.classList.add("fade-out");

    setTimeout(() => {
        window.location.href = "game.html";
    }, 500);
}

document.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        startGame();
    }
});

document.addEventListener("click", () => {
    startGame();
});