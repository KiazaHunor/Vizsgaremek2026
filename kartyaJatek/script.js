let currentChallenger = null; // "player" | "enemy"
    let phase = "waiting";       // waiting | chooseStat | chooseCard | battle
    let playerScore = 0;
    let enemyScore = 0;
    let selectedCardIndex = null;
    let selectedStat = null;
    const playerDeck = document.getElementById("player-deck");
    const playerHand = document.getElementById("player-hand");
    const enemyHand = document.getElementById("enemy-hand");

    let playerCards = [];
    let enemyCards = [];

    playerDeck.addEventListener("click", dealCards);

    function shuffle(array) {
        return [...array].sort(() => Math.random() - 0.5);
    }

    function dealCards() {
        playerHand.innerHTML = "";
        enemyHand.innerHTML = "";

        const shuffled = shuffle(players);

        playerCards = shuffled.slice(0, 5);
        enemyCards = shuffled.slice(5, 10);

        renderHands();
        currentChallenger = Math.random() < 0.5 ? "player" : "enemy";
        phase = "chooseStat";

        if (currentChallenger === "player") 
        {
            showMessage("Te kezdesz! Válassz egy statot!");
        } 
        else 
        {
            showMessage("Az ellenfél kezd! Várd meg a kihívást!");
            enemyChooseStat();
        }
    }

    function renderHands() {
        playerHand.innerHTML = "";
        enemyHand.innerHTML = "";

        // Ellenfél lapjai (hátoldal)
        enemyCards.forEach(() => {
            const card = document.createElement("div");
            card.className = "card back";
            enemyHand.appendChild(card);
        });

        // Játékos lapjai
        playerCards.forEach((player, index) => {
            const card = document.createElement("div");
            card.className = "card";
            card.innerHTML = `
                <strong>${player.name}</strong><br><br>
                ATK: ${player.attack}<br>
                CTRL: ${player.controll}<br>
                DEF: ${player.defence}
            `;
        card.addEventListener("click", () => {
            selectCard(index);
    });
        playerHand.appendChild(card);
    });
    }
    function selectCard(index) 
    {
            function selectCard(index) 
            {
                if (phase !== "chooseCard") 
                    {
                        showMessage("Előbb statot kell választani!");
                        return;
                    }

                const allCards = document.querySelectorAll(".player-hand .card");
                allCards.forEach(card => card.classList.remove("selected"));
                selectedCardIndex = index;
                allCards[index].classList.add("selected");

                showMessage("Kártya kiválasztva. Kör lejátszható!");
            }

        // Régi kijelölés törlése
        const allCards = document.querySelectorAll(".player-hand .card");
        allCards.forEach(card => card.classList.remove("selected"));

        // Új kijelölés
        selectedCardIndex = index;
        allCards[index].classList.add("selected");

        console.log("Kiválasztott lap:", playerCards[index]);
    }
    document.querySelectorAll(".stat-buttons button").forEach(button => {
    button.addEventListener("click", () => {

        if (phase !== "chooseStat") return;

        if (currentChallenger !== "player") {
            showMessage("Most nem te hívsz ki!");
            return;
        }

        selectedStat = button.dataset.stat;

        document.querySelectorAll(".stat-buttons button")
            .forEach(btn => btn.classList.remove("selected"));

        button.classList.add("selected");

        phase = "chooseCard";

        showMessage("Stat kiválasztva: " + selectedStat + ". Most válassz kártyát!");
        });
    });



    document.getElementById("play-round").addEventListener("click", playRound);

    function playRound() 
    {

        // 1️⃣ Fázis ellenőrzés
        if (phase !== "chooseCard") {
            showMessage("Most nem tudsz játszani!");
            return;
        }

        if (selectedCardIndex === null || !selectedStat) {
            showMessage("Válassz kártyát és statot!");
            return;
        }

        phase = "battle";

        const enemyIndex = Math.floor(Math.random() * enemyCards.length);

        const playerCard = playerCards[selectedCardIndex];
        const enemyCard = enemyCards[enemyIndex];

        const playerValue = playerCard[selectedStat];
        const enemyValue = enemyCard[selectedStat];

        // 2️⃣ Kártyák kirajzolása (beúsznak)
        showBattleCards(playerCard, enemyCard, selectedStat);

        // 3️⃣ STAT VILLOGÁS + WIN/LOSE ANIMÁCIÓ
        setTimeout(() => {

        const playerCardDiv = document.getElementById("player-battle");
        const enemyCardDiv = document.getElementById("enemy-battle");

        const playerStatRow = playerCardDiv.querySelector(`.${selectedStat}`).parentElement;
        const enemyStatRow = enemyCardDiv.querySelector(`.${selectedStat}`).parentElement;

        // Stat villogás
        playerStatRow.classList.add("stat-highlight");
        enemyStatRow.classList.add("stat-highlight");

        setTimeout(() => 
        {
            if (playerValue > enemyValue) {
                playerCardDiv.classList.add("winner");
                enemyCardDiv.classList.add("loser");
            } else if (playerValue < enemyValue) {
                enemyCardDiv.classList.add("winner");
                playerCardDiv.classList.add("loser");
            }
        }, 700);
    }, 700);


    // 4️⃣ EREDMÉNY + PONTFRISSÍTÉS + KÖR LEZÁRÁS
        setTimeout(() => 
        {
            let resultText = "";

            if (playerValue > enemyValue) {
                playerScore++;
                document.getElementById("player-score").textContent = playerScore;
                resultText = "Győztél!";
            } else if (playerValue < enemyValue) {
                enemyScore++;
                document.getElementById("enemy-score").textContent = enemyScore;
                resultText = "Vesztettél!";
            } else {
                resultText = "Döntetlen!";
            }

            showMessage(resultText);

            // Kártyák eltávolítása
            playerCards.splice(selectedCardIndex, 1);
            enemyCards.splice(enemyIndex, 1);

            // RESET BATTLE AREA
            const playerBattle = document.getElementById("player-battle");
            const enemyBattle = document.getElementById("enemy-battle");

            playerBattle.className = "battle-card";
            enemyBattle.className = "battle-card";

            playerBattle.style.opacity = "0";
            enemyBattle.style.opacity = "0";

            setTimeout(() => {
                playerBattle.innerHTML = "";
                enemyBattle.innerHTML = "";
            }, 400);

            // Reset state
            selectedCardIndex = null;
            selectedStat = null;

            document.querySelectorAll(".stat-buttons button")
                .forEach(btn => btn.classList.remove("selected"));

            renderHands();

            phase = "chooseCard";

            if (playerCards.length === 0) {
                endGame();
            }
        }, 2200); // időzítés az animációkhoz igazítva
    }


    function showBattleCards(playerCard, enemyCard, selectedStat) 
    {
        const playerDiv = document.getElementById("player-battle");
        const enemyDiv = document.getElementById("enemy-battle");

        // Kezdő állapot
        playerDiv.className = "battle-card player-start";
        enemyDiv.className = "battle-card enemy-start";

        // Kártya HTML
        playerDiv.innerHTML = createBattleCardHTML(playerCard, selectedStat);
        enemyDiv.innerHTML = createBattleCardHTML(enemyCard, selectedStat);

        // KÖZÉPRE CSÚSZÁS
        setTimeout(() => {
            playerDiv.classList.add("battle-active");
            enemyDiv.classList.add("battle-active");
        }, 50);
    }

    function enemyChooseStat() 
    {
        const stats = ["attack", "controll", "defence"];
        selectedStat = stats[Math.floor(Math.random() * stats.length)];

        showMessage("Ellenfél kihívott erre: " + selectedStat.toUpperCase());
        phase = "chooseCard";
    }

    function showMessage(text, duration = 2000) 
    {
        const msg = document.getElementById("game-message");
        msg.textContent = text;

        msg.classList.add("show");

        setTimeout(() => {
            msg.classList.remove("show");
        }, duration);
    }
    function createBattleCardHTML(card, selectedStat) 
    {
        return `
            <h3>${card.name}</h3>
            <div class="stat ${selectedStat === 'attack' ? 'selected-stat' : ''}">
                Attack: <span class="stat-value attack">${card.attack}</span>
            </div>
            <div class="stat ${selectedStat === 'controll' ? 'selected-stat' : ''}">
                Controll: <span class="stat-value controll">${card.controll}</span>
            </div>
            <div class="stat ${selectedStat === 'defence' ? 'selected-stat' : ''}">
                Defence: <span class="stat-value defence">${card.defence}</span>
            </div>
        `;
    }


