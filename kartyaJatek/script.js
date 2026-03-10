let currentChallenger = null; // "player" | "enemy"
let phase = "waiting"; // waiting | chooseStat | chooseCard | battle | enemyThinking | finished
let roundLocked = false;

let playerScore = 0;
let enemyScore = 0;

let selectedCardIndex = null;
let selectedStat = null;

const playerDeck = document.getElementById("player-deck");
const playerHand = document.getElementById("player-hand");
const enemyHand = document.getElementById("enemy-hand");
const playRoundBtn = document.getElementById("play-round");

let playerCards = [];
let enemyCards = [];

// Paklira kattintás
playerDeck.addEventListener("click", () => {
    if (phase === "battle" || roundLocked) return;
    dealCards();
});

// Kör lejátszása gomb
playRoundBtn.addEventListener("click", playRound);

// Stat gombok

function shuffle(array) {
    return [...array].sort(() => Math.random() - 0.5);
}

function dealCards() {
    resetBattleArea(true);

    const shuffled = shuffle(players);

    playerCards = shuffled.slice(0, 5);
    enemyCards = shuffled.slice(5, 10);

    playerScore = 0;
    enemyScore = 0;
    updateScoreboard();

    selectedCardIndex = null;
    selectedStat = null;
    roundLocked = false;

    document.querySelectorAll(".stat-buttons button")
        .forEach(btn => btn.classList.remove("selected"));

    renderHands();

    currentChallenger = Math.random() < 0.5 ? "player" : "enemy";
    startNextTurn();
}

function getOverall(card) {
    const atk = Number(card.attack) || 0;
    const ctrl = Number(card.controll) || 0;
    const def = Number(card.defence) || 0;
    return Math.round((atk + ctrl + def) / 3);
}

function getPositionLabel(card) {
    return card.position || "N/A";
}

function getTeamLabel(card) {
    return card.team || "Ismeretlen csapat";
}

function getKitImage(card) {
    return card.shirt_image || "hatternelkul/default.png";
}

function createCardHTML(card, selectedStat = null, cardIndex = null, clickableStats = false) {
    return `
        <div class="card-top">
            <div class="card-rating">${getOverall(card)}</div>
            <div class="card-position">${getPositionLabel(card)}</div>
        </div>

        <div class="card-image">
            <img src="${getKitImage(card)}" alt="${getTeamLabel(card)} mez">
        </div>

        <div class="card-name">${card.name ?? "Ismeretlen játékos"}</div>
        <div class="card-team">${getTeamLabel(card)}</div>

        <div class="card-stats">
            <div class="stat-box ${selectedStat === "attack" ? "selected-stat" : ""} ${clickableStats ? "stat-clickable" : ""}" data-stat="attack" data-card-index="${cardIndex ?? ""}">
                <span>ATK</span>
                <strong class="attack">${Number(card.attack) || 0}</strong>
            </div>
            <div class="stat-box ${selectedStat === "controll" ? "selected-stat" : ""} ${clickableStats ? "stat-clickable" : ""}" data-stat="controll" data-card-index="${cardIndex ?? ""}">
                <span>CTRL</span>
                <strong class="controll">${Number(card.controll) || 0}</strong>
            </div>
            <div class="stat-box ${selectedStat === "defence" ? "selected-stat" : ""} ${clickableStats ? "stat-clickable" : ""}" data-stat="defence" data-card-index="${cardIndex ?? ""}">
                <span>DEF</span>
                <strong class="defence">${Number(card.defence) || 0}</strong>
            </div>
        </div>
    `;
}

function renderHands() {
    playerHand.innerHTML = "";
    enemyHand.innerHTML = "";

    enemyCards.forEach(() => {
        const card = document.createElement("div");
        card.className = "card back";
        enemyHand.appendChild(card);
    });

    playerCards.forEach((player, index) => {
        const card = document.createElement("div");
        card.className = "card";

        const clickableStats = (phase === "chooseStat" && currentChallenger === "player");
        const activeStat = selectedCardIndex === index ? selectedStat : null;

        card.innerHTML = createCardHTML(player, activeStat, index, clickableStats);

        // Ha az ellenfél hívott, akkor teljes lapra kattintva választasz
        if (phase === "chooseCard" && currentChallenger === "enemy") {
            card.addEventListener("click", () => {
                selectCard(index);
            });
        }

        if (selectedCardIndex === index) {
            card.classList.add("selected");
        }

        playerHand.appendChild(card);
    });

    // Stat kattintások felrakása utólag
    document.querySelectorAll(".player-hand .stat-clickable").forEach(statEl => {
        statEl.addEventListener("click", (e) => {
            e.stopPropagation();

            const index = Number(statEl.dataset.cardIndex);
            const statName = statEl.dataset.stat;

            selectCardStat(index, statName);
        });
    });
}

function selectCard(index) {
    if (roundLocked) return;

    if (phase !== "chooseCard") {
        showMessage("Előbb statot kell választani!");
        return;
    }

    const allCards = document.querySelectorAll(".player-hand .card");
    allCards.forEach(card => card.classList.remove("selected"));

    selectedCardIndex = index;
    if (allCards[index]) {
        allCards[index].classList.add("selected");
    }

    showMessage("Kártya kiválasztva. Kör lejátszható!");
}

function startNextTurn() {
    selectedCardIndex = null;
    selectedStat = null;

    document.querySelectorAll(".stat-buttons button")
        .forEach(btn => btn.classList.remove("selected"));

    renderHands();

    if (playerCards.length === 0 || enemyCards.length === 0) {
        endGame();
        return;
    }

    roundLocked = false;

    if (currentChallenger === "player") {
        phase = "chooseStat";
        showMessage("Te hívsz! Válassz statot!");
    } else {
        phase = "enemyThinking";
        showMessage("Az ellenfél gondolkodik...");
        setTimeout(() => {
            enemyChooseStat();
        }, 500);
    }
}

function enemyChooseStat() {
    if (phase === "finished") return;

    const stats = ["attack", "controll", "defence"];
    selectedStat = stats[Math.floor(Math.random() * stats.length)];

    document.querySelectorAll(".stat-buttons button")
        .forEach(btn => btn.classList.remove("selected"));

    phase = "chooseCard";
    showMessage("Az ellenfél kihívott erre: " + selectedStat.toUpperCase() + ". Válassz egy kártyát!");
}

// Okosabb enemy lapválasztás: a kiválasztott statban legerősebb lapot játssza ki
function getBestEnemyCardIndex(stat) {
    let bestIndex = 0;
    let bestValue = -Infinity;

    enemyCards.forEach((card, index) => {
        const value = Number(card[stat]) || 0;
        if (value > bestValue) {
            bestValue = value;
            bestIndex = index;
        }
    });

    return bestIndex;
}

function playRound() {
    if (roundLocked) return;

    if (phase !== "chooseCard") {
        showMessage("Előbb statot és kártyát kell választani!");
        return;
    }

    if (selectedCardIndex === null || !selectedStat) {
        showMessage("Válassz kártyát és statot!");
        return;
    }

    if (!playerCards[selectedCardIndex]) {
        showMessage("Érvénytelen játékoslap!");
        return;
    }

    roundLocked = true;
    phase = "battle";

    // Az ellenfél mindig a legjobb lapját választja az adott statra
    const enemyIndex = getBestEnemyCardIndex(selectedStat);

    const playerCard = playerCards[selectedCardIndex];
    const enemyCard = enemyCards[enemyIndex];

    const playerValue = Number(playerCard[selectedStat]) || 0;
    const enemyValue = Number(enemyCard[selectedStat]) || 0;

    showBattleCards(playerCard, enemyCard, selectedStat);

    // Stat highlight
    setTimeout(() => {
        const playerCardDiv = document.getElementById("player-battle");
        const enemyCardDiv = document.getElementById("enemy-battle");

        const playerStatValue = playerCardDiv.querySelector(`.${selectedStat}`);
        const enemyStatValue = enemyCardDiv.querySelector(`.${selectedStat}`);

        if (playerStatValue && enemyStatValue) {
            playerStatValue.parentElement.classList.add("stat-highlight");
            enemyStatValue.parentElement.classList.add("stat-highlight");
        }

        // Win / lose animáció
        setTimeout(() => {
            if (playerValue > enemyValue) {
                playerCardDiv.classList.add("winner");
                enemyCardDiv.classList.add("loser");
            } else if (playerValue < enemyValue) {
                enemyCardDiv.classList.add("winner");
                playerCardDiv.classList.add("loser");
            }
        }, 1000);
    }, 1000);

    // Kör lezárása
    setTimeout(() => {
        let resultText = "";

        if (playerValue > enemyValue) {
            playerScore++;
            currentChallenger = "player";
            resultText = "Győztél! Te hívsz a következő körben.";
        } else if (playerValue < enemyValue) {
            enemyScore++;
            currentChallenger = "enemy";
            resultText = "Vesztettél! Az ellenfél hív a következő körben.";
        } else {
            resultText = "Döntetlen! Ugyanaz hív, mint előző körben.";
        }

        updateScoreboard();
        showMessage(resultText);

        // Kártyák eltávolítása
        playerCards.splice(selectedCardIndex, 1);
        enemyCards.splice(enemyIndex, 1);

        resetBattleArea(false);

        setTimeout(() => {
            startNextTurn();
        }, 1000);

    }, 3000);
}

function showBattleCards(playerCard, enemyCard, selectedStat) {
    const playerDiv = document.getElementById("player-battle");
    const enemyDiv = document.getElementById("enemy-battle");

    playerDiv.style.opacity = "";
    enemyDiv.style.opacity = "";
    playerDiv.style.transform = "";
    enemyDiv.style.transform = "";

    playerDiv.className = "battle-card player-start";
    enemyDiv.className = "battle-card enemy-start";

    playerDiv.innerHTML = createCardHTML(playerCard, selectedStat);
    enemyDiv.innerHTML = createCardHTML(enemyCard, selectedStat);

    setTimeout(() => {
        playerDiv.classList.add("battle-active");
        enemyDiv.classList.add("battle-active");
    }, 50);
}



function updateScoreboard() {
    document.getElementById("player-score").textContent = playerScore;
    document.getElementById("enemy-score").textContent = enemyScore;
}

function resetBattleArea(clearNow = false) {
    const playerBattle = document.getElementById("player-battle");
    const enemyBattle = document.getElementById("enemy-battle");

    playerBattle.className = "battle-card";
    enemyBattle.className = "battle-card";

    // FONTOS: ne állíts inline opacity-t
    playerBattle.style.opacity = "";
    enemyBattle.style.opacity = "";
    playerBattle.style.transform = "";
    enemyBattle.style.transform = "";

    if (clearNow) {
        playerBattle.innerHTML = "";
        enemyBattle.innerHTML = "";
    } else {
        setTimeout(() => {
            playerBattle.innerHTML = "";
            enemyBattle.innerHTML = "";
        }, 500);
    }
}

function endGame() {
    roundLocked = true;
    phase = "finished";

    let message = "";

    if (playerScore > enemyScore) {
        message = "Vége a játéknak! Te nyertél!";
    } else if (playerScore < enemyScore) {
        message = "Vége a játéknak! Az ellenfél nyert!";
    } else {
        message = "Vége a játéknak! Döntetlen!";
    }

    showMessage(message + " Kattints újra a paklira az új játékhoz!", 5000);
}

function showMessage(text, duration = 5000) {
    const msg = document.getElementById("game-message");
    msg.textContent = text;
    msg.classList.add("show");

    setTimeout(() => {
        msg.classList.remove("show");
    }, duration);
}

function safeStat(value) {
    return value ?? 0;
}

function selectCardStat(index, statName) {
    if (roundLocked) return;

    if (phase !== "chooseStat") {
        showMessage("Most nem választhatsz statot!");
        return;
    }

    if (currentChallenger !== "player") {
        showMessage("Ebben a körben az ellenfél hív ki!");
        return;
    }

    selectedCardIndex = index;
    selectedStat = statName;
    phase = "chooseCard";

    renderHands();
    showMessage(`Kiválasztottad: ${statName.toUpperCase()}. Most játszd le a kört!`);
}