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
            alert("Te kezdesz! Válassz egy statot!");
        } 
        else 
        {
            alert("Az ellenfél kezd! Várd meg a kihívást!");
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
                        alert("Előbb statot kell választani!");
                        return;
                    }

                const allCards = document.querySelectorAll(".player-hand .card");
                allCards.forEach(card => card.classList.remove("selected"));
                selectedCardIndex = index;
                allCards[index].classList.add("selected");

                alert("Kártya kiválasztva. Kör lejátszható!");
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
            alert("Most nem te hívsz ki!");
            return;
        }

        selectedStat = button.dataset.stat;

        document.querySelectorAll(".stat-buttons button")
            .forEach(btn => btn.classList.remove("selected"));

        button.classList.add("selected");

        phase = "chooseCard";

        alert("Stat kiválasztva: " + selectedStat + ". Most válassz kártyát!");
        });
    });



    document.getElementById("play-round").addEventListener("click", playRound);

    function playRound() 
    {
            if (phase !== "chooseCard") {
                alert("Még nem tartunk ott!");
                return;
            }

            if (selectedCardIndex === null || !selectedStat) {
                alert("Hiányzik a stat vagy a kártya!");
                return;
            }

            phase = "battle";

            const enemyIndex = Math.floor(Math.random() * enemyCards.length);

            const playerCard = playerCards[selectedCardIndex];
            const enemyCard = enemyCards[enemyIndex];

            const playerValue = playerCard[selectedStat];
            const enemyValue = enemyCard[selectedStat];

            showBattleCards(playerCard, enemyCard, selectedStat);

            let winner = null;
            let result = "";

            if (playerValue > enemyValue) {
                result = "Győztél!";
                winner = "player";
            } 
            else if (playerValue < enemyValue) {
                result = "Vesztettél!";
                winner = "enemy";
            } 
            else {
                result = "Döntetlen!";
                winner = "draw";
            }

            setTimeout(() => 
            {
                if (winner === "player") {
                    playerScore++;
                    document.getElementById("player-score").textContent = playerScore;
                    currentChallenger = "player";
                } 
                else if (winner === "enemy") {
                    enemyScore++;
                    document.getElementById("enemy-score").textContent = enemyScore;
                    currentChallenger = "enemy";
                }

                alert(result);

                playerCards.splice(selectedCardIndex, 1);
                enemyCards.splice(enemyIndex, 1);

                selectedCardIndex = null;
                selectedStat = null;

                document.querySelectorAll(".stat-buttons button")
                    .forEach(btn => btn.classList.remove("selected"));

                document.getElementById("player-battle").innerHTML = "";
                document.getElementById("enemy-battle").innerHTML = "";

                renderHands();

                if (playerCards.length === 0) {
                    endGame();
                    return;
                }

                // 🔥 EZ HIÁNYZOTT NÁLAD
                phase = "chooseStat";

                if (currentChallenger === "player") {
                    alert("Te hívsz ki!");
                } else {
                    enemyChooseStat();
                }

            }, 800);
    }


    function showBattleCards(playerCard, enemyCard, stat) 
    {
        const playerBattle = document.getElementById("player-battle");
        const enemyBattle = document.getElementById("enemy-battle");

        playerBattle.innerHTML = "";
        enemyBattle.innerHTML = "";

        const pCard = document.createElement("div");
        pCard.className = "card";
        pCard.innerHTML = `
            <strong>${playerCard.name}</strong><br><br>
            ${stat.toUpperCase()}: ${playerCard[stat]}
        `;

        const eCard = document.createElement("div");
        eCard.className = "card";
        eCard.innerHTML = `
            <strong>${enemyCard.name}</strong><br><br>
            ${stat.toUpperCase()}: ${enemyCard[stat]}
        `;

        playerBattle.appendChild(pCard);
        enemyBattle.appendChild(eCard);
    }

    function enemyChooseStat() 
    {
        const stats = ["attack", "controll", "defence"];
        selectedStat = stats[Math.floor(Math.random() * stats.length)];

        alert("Ellenfél kihívott erre: " + selectedStat.toUpperCase());
        phase = "chooseCard";
    }


