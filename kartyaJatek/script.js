let currentChallenger = null; // "player" | "enemy"
let phase = "waiting"; // waiting | chooseStat | chooseCard | battle | enemyThinking | finished
let roundLocked = false;

let playerScore = 0;
let enemyScore = 0;

let selectedCardIndex = null;
let selectedStat = null;

let deckLocked = false;

const playerDeck = document.getElementById("player-deck");
const playerHand = document.getElementById("player-hand");
const enemyHand = document.getElementById("enemy-hand");
const playRoundBtn = document.getElementById("play-round");
const restartBtn = document.getElementById("restart-game");

let playerCards = [];
let enemyCards = [];

/* ===== IDŐZÍTÉSEK ===== */
const TIMINGS = {
    enemyThinking: 900,
    challengerMessage: 1400,
    battleSlideIn: 250,
    statHighlight: 900,
    winnerAnimation: 1700,
    roundResultDelay: 2600,
    nextTurnDelay: 1800,
    battleClearDelay: 900,
    normalMessage: 1400,
    endMessage: 3200
};

// Paklira kattintás
playerDeck.addEventListener("click", () => {
    if (phase === "battle" || roundLocked || deckLocked) return;

    dealCards();
    deckLocked = true;
});

// Kör lejátszása gomb
playRoundBtn.addEventListener("click", () => {
    console.log("PLAY BUTTON KATT");
    playRound();
});

function shuffle(array) {
    return [...array].sort(() => Math.random() - 0.5);
}

function dealCards() {
    if (deckLocked) return;

    if (!players || players.length < 10) {
        showMessage("A játékosok még töltődnek vagy nincs elég adat!", 1500);
        return;
    }

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

    renderHands();

    currentChallenger = Math.random() < 0.5 ? "player" : "enemy";
    startNextTurn();
}

function getOverall(card) {
    return Math.round(Number(card.rating) || 0);
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
        const activeStat = selectedCardIndex === index ? selectedStat : (currentChallenger === "enemy" ? selectedStat : null);

        card.innerHTML = createCardHTML(player, activeStat, index, clickableStats);

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
        showMessage("Most nem választhatsz kártyát!");
        return;
    }

    if (currentChallenger !== "enemy") {
        showMessage("Ebben a körben a kártyán lévő statra kell kattintanod!");
        return;
    }

    selectedCardIndex = index;

    const allCards = document.querySelectorAll(".player-hand .card");
    allCards.forEach(card => card.classList.remove("selected"));

    if (allCards[index]) {
        allCards[index].classList.add("selected");
    }

    showMessage("Kártya kiválasztva. Kör lejátszható!", 1500);
}

function startNextTurn() {
    document.getElementById("restart-game").style.display = "none";
    selectedCardIndex = null;
    selectedStat = null;

    //renderHands();

    if (playerCards.length === 0 || enemyCards.length === 0) {
        endGame();
        return;
    }

    roundLocked = false;

    if (currentChallenger === "player") {
        phase = "chooseStat";
        renderHands();
        showMessage("Te hívsz! Kattints egy statra a kiválasztott kártyán!", TIMINGS.challengerMessage);
        
    } else {
        phase = "enemyThinking";
        renderHands();
        showMessage("Az ellenfél gondolkodik...", TIMINGS.enemyThinking);
        setTimeout(() => {
            enemyChooseStat();
        }, TIMINGS.enemyThinking);
    }
}

function enemyChooseStat() {
    if (phase === "finished") return;

    const stats = ["attack", "controll", "defence"];
    selectedStat = stats[Math.floor(Math.random() * stats.length)];

    selectedCardIndex = null;
    roundLocked = false;
    phase = "chooseCard";
    
    renderHands();
    
    showMessage(
        "Az ellenfél kihívott erre: " + selectedStat.toUpperCase() + ". Válassz egy kártyát!",
        TIMINGS.challengerMessage
    );
}

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
    console.log("PLAY ROUND START");
    console.log("phase:", phase);
    console.log("roundLocked:", roundLocked);
    console.log("selectedCardIndex:", selectedCardIndex);
    console.log("selectedStat:", selectedStat);
    console.log("currentChallenger:", currentChallenger);

    if (roundLocked) return;

    if (phase !== "chooseCard") {
        showMessage("Előbb statot és kártyát kell választani!", 1500);
        return;
    }

    if (selectedCardIndex === null || !selectedStat) {
        showMessage("Válassz kártyát és statot!", 1500);
        return;
    }

    if (!playerCards[selectedCardIndex]) {
        showMessage("Érvénytelen játékoslap!", 1500);
        return;
    }

    roundLocked = true;
    phase = "battle";

    const enemyIndex = getBestEnemyCardIndex(selectedStat);

    const playerCard = playerCards[selectedCardIndex];
    const enemyCard = enemyCards[enemyIndex];

    const playerValue = Number(playerCard[selectedStat]) || 0;
    const enemyValue = Number(enemyCard[selectedStat]) || 0;

    showBattleCards(playerCard, enemyCard, selectedStat);

    setTimeout(() => {
        const playerCardDiv = document.getElementById("player-battle");
        const enemyCardDiv = document.getElementById("enemy-battle");

        const playerStatValue = playerCardDiv.querySelector(`.${selectedStat}`);
        const enemyStatValue = enemyCardDiv.querySelector(`.${selectedStat}`);

        if (playerStatValue && enemyStatValue) {
            playerStatValue.parentElement.classList.add("stat-highlight");
            enemyStatValue.parentElement.classList.add("stat-highlight");
        }
    }, TIMINGS.statHighlight);

    setTimeout(() => {
        const playerCardDiv = document.getElementById("player-battle");
        const enemyCardDiv = document.getElementById("enemy-battle");

        if (playerValue > enemyValue) {
            playerCardDiv.classList.add("winner");
            enemyCardDiv.classList.add("loser");
        } else if (playerValue < enemyValue) {
            enemyCardDiv.classList.add("winner");
            playerCardDiv.classList.add("loser");
        }
    }, TIMINGS.winnerAnimation);

    setTimeout(() => {
        let resultText = "";

        if (playerValue > enemyValue) {
            playerScore++;
            currentChallenger = "player";
            resultText = "Te nyertél!";
        } else if (playerValue < enemyValue) {
            enemyScore++;
            currentChallenger = "enemy";
            resultText = "Az ellenfél nyert!";
        } else {
            resultText = "Döntetlen!";
        }

        updateScoreboard();
        showMessage(resultText, TIMINGS.nextTurnDelay);

        playerCards.splice(selectedCardIndex, 1);
        enemyCards.splice(enemyIndex, 1);

        resetBattleArea(false);

        setTimeout(() => {
            startNextTurn();
        }, TIMINGS.nextTurnDelay);
    }, TIMINGS.roundResultDelay);
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
    }, TIMINGS.battleSlideIn);
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

    playerBattle.style.opacity = "";
    enemyBattle.style.opacity = "";
    playerBattle.style.transform = "";
    enemyBattle.style.transform = "";

    if (clearNow) 
    {
        playerBattle.innerHTML = "";
        enemyBattle.innerHTML = "";
    }
    else 
    {
        setTimeout(() => {
            playerBattle.innerHTML = "";
            enemyBattle.innerHTML = "";
        }, TIMINGS.battleClearDelay);
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

    showMessage(message, TIMINGS.endMessage);
}

function showMessage(text, duration = TIMINGS.normalMessage) {
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
        showMessage("Most nem választhatsz statot!", 900);
        return;
    }

    if (currentChallenger !== "player") {
        showMessage("Ebben a körben az ellenfél hív ki!", 900);
        return;
    }

    selectedCardIndex = index;
    selectedStat = statName;
    phase = "chooseCard";

    renderHands();
    showMessage(`Kiválasztottad: ${statName.toUpperCase()}. Most játszd le a kört!`, 1200);
}
restartBtn.addEventListener("click", () => {
    restartGame();
});

function restartGame() {
    document.getElementById("restart-game").style.display = "none";

    playerScore = 0;
    enemyScore = 0;

    selectedCardIndex = null;
    selectedStat = null;

    currentChallenger = null;
    phase = "waiting";
    roundLocked = false;
    deckLocked = false;

    updateScoreboard();

    resetBattleArea(true);
    dealCards();
    deckLocked = true;
}
function endGame() {
    roundLocked = true;
    phase = "finished";

    let message = "";
    let result = "";

    if (playerScore > enemyScore) {
        message = "Vége a játéknak! Te nyertél!";
        result = "win";
    } else if (playerScore < enemyScore) {
        message = "Vége a játéknak! Az ellenfél nyert!";
        result = "loss";
    } else {
        message = "Vége a játéknak! Döntetlen!";
        result = "draw";
    }

    showMessage(message, TIMINGS.endMessage);

    saveGameResult(result,playerScore, enemyScore);

    const restartBtn = document.getElementById("restart-game");
    if(restartBtn)
    {
        restartBtn.style.display = "inline-block";
    }
}
function saveGameResult(result, playerScore, enemyScore) {
    const token = localStorage.getItem("token");

    if (!token) {
        console.log("Nincs bejelentkezett felhasználó, a statisztika nem kerül mentésre.");
        return;
    }

    fetch("save_game_result.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({
            result: result,
            player_score: playerScore,
            enemy_score: enemyScore
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Mentés válasz:", data);
    })
    .catch(error => {
        console.error("Hiba az eredmény mentése közben:", error);
    });
}